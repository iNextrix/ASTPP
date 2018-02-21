// 
// ASTPP - Open Source Voip Billing
//
// Copyright (C) 2004, Aleph Communications
// Author: Sergey Tankovich (2005)
// Owner: Darren Wiebe (darren@aleph-com.net)
//
// This program is Free Software and is distributed under the
// terms of the GNU General Public License version 2.
//
// This is an LCR and credit authorizing module for Asterisk(TM)
// and ASTPP.   (www.astpp.org)
//
//

#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <signal.h>
#include <unistd.h>
#include <stdio.h>

#include <asterisk/channel.h>
#include <asterisk/features.h>
#include <asterisk/file.h>
#include <asterisk/lock.h>
#include <asterisk/logger.h>
#include <asterisk/module.h>
#include <asterisk/options.h>
#include <asterisk/pbx.h>

#include <pthread.h>

#include <mysql/mysql.h>
#include <mysql/errmsg.h>

#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>


#include "asterisk.h"
#include "asterisk/app.h"


#define ASTPP_CONFIG_FILE "/var/lib/astpp/astpp-config.conf"
#define ASTPP_MAX_VARS 16
// ASTPP_MAX_VARS allows you to limit maximum umber of variables set up by ASTPP_LCR
// Lower number of routes will speedup work on big tables

struct astpp_conf {
	char dbhost[32];
	char dbname[32];
	char dbuser[32];
	char dbpass[32];
	char enablelcr[8];
} c;


static char *tdesc = "ASTPP binary functions: ASTPP_LCR(),ASTPP_AUTH()";



static char *app1 = "ASTPP_LCR";
static char *synopsis1 = "ASTPP Low Cost Routing";
static char *descrip1 =
" Searches for Low Cost Routes in ASTPP database\n"
" Each found route placed into Variable, LCRSTRING1..LCRSTRINGN\n"
" Best route in LCRSTRING1, worst in LCRSTRINGN\n"
"\n"
"\n"
" [astpp-lcr]\n"
"\n"
" exten => _1XXXXXXXXXX,1,ASTPP_LCR(${EXTEN})\n"
" exten => _1XXXXXXXXXX,2,Dial(${LCRSTRING1})\n"
" exten => _1XXXXXXXXXX,103,Busy\n"
" exten => _1XXXXXXXXXX,3,Dial(${LCRSTRING2})\n"
" exten => _1XXXXXXXXXX,104,Busy\n"
" exten => _1XXXXXXXXXX,4,Dial(${LCRSTRING3})\n"
" exten => _1XXXXXXXXXX,105,Busy\n"
" exten => _1XXXXXXXXXX,5,Dial(${LCRSTRING4})\n"
" exten => _1XXXXXXXXXX,106,Busy\n"
" exten => _1XXXXXXXXXX,6,Dial(${LCRSTRING5})\n"
" exten => _1XXXXXXXXXX,107,Busy\n";



static char *app2 = "ASTPP_AUTH";
static char *synopsis2 = "ASTPP Card Auth";
static char *descrip2 =
" Card-number and number to dial derived from command-line.\n"
" Call script with the card-number as first arg and the number\n"
" to dial as the second arg.  astpp-authorize will return a line containing info\n"
" that will cut the call off before it goes over the users credit limit.  The\n"
" user can get over credit limit if they have multiple calls going at once.\n"
" Presently the only way to stop that is to limit them to one call which is not\n"
" a nice solution.\n"
"\n"
" The following variables must be set:\n"
"  USER1 = Username at IAX2 Provider #1\n"
"  PASS1 = Password for User1\n"
"  PROVIDER1 = IP address of IAX2 Provider #1 or else associate name from\n"
"     iax.conf\n"
"\n"
" With LCR\n"
" [astpp]\n"
"\n"
"exten => _1XXXXXXXXXX,1,ASTPP_AUTH(${CARD}|${EXTEN})\n"
"exten => _1XXXXXXXXXX,2,GotoIf($[\"${CALLSTATUS}\" = \"0\"]?60)\n"
"exten => _1XXXXXXXXXX,3,GotoIf($[\"${CALLSTATUS}\" = \"1\"]?70)\n"
"exten => _1XXXXXXXXXX,4,GotoIf($[\"${CALLSTATUS}\" = \"2\"]?80)\n"
"exten => _1XXXXXXXXXX,5,Dial(${LCRSTRING1}||${TIMELIMIT}|${OPTIONS})\n"
"exten => _1XXXXXXXXXX,106,Busy\n"
"exten => _1XXXXXXXXXX,6,Dial(${LCRSTRING2})\n"
"exten => _1XXXXXXXXXX,107,Busy\n"
"exten => _1XXXXXXXXXX,7,Dial(${LCRSTRING3})\n"
"exten => _1XXXXXXXXXX,108,Busy\n"
"exten => _1XXXXXXXXXX,8,Dial(${LCRSTRING4})\n"
"exten => _1XXXXXXXXXX,109,Busy\n"
"exten => _1XXXXXXXXXX,9,Dial(${LCRSTRING5})\n"
"exten => _1XXXXXXXXXX,110,Busy\n"
"exten => _1XXXXXXXXXX,60,Congestion ; '0' Tells them they do not have enough money\n"
"exten => _1XXXXXXXXXX,61,Hangup\n"
"exten => _1XXXXXXXXXX,70,Congestion '1' Bad Phone Number\n"
"exten => _1XXXXXXXXXX,71,Hangup\n"
"exten => _1XXXXXXXXXX,80,Congestion\n"
"exten => _1XXXXXXXXXX,81,Hangup\n"
"; This lines are optional and would forward users to a help desk if the call did not go through.\n"
"exten => _1XXXXXXXXXX,60,Dial(SIP/HELPDESK) ; '0' Tells them they do not have enough money\n"
"exten => _1XXXXXXXXXX,61,Hangup\n";




STANDARD_LOCAL_USER;
LOCAL_USER_DECL;


static int astpp_load_config(const char* filename,struct astpp_conf *c) {
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_load_config() invoked\n");
	FILE *fh = fopen(filename, "rt");
	if(!fh) {
		ast_verbose(VERBOSE_PREFIX_3 "ERROR: Can't open file '%s'\n",filename);
		return(0);
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "  |-> file opened '%s'\n",filename);
	}
	char buf[1024],*val,*end,*comment;
	while(fgets(buf,1024,fh)) {
		//perfoming chomp()
		int len = strlen(buf);
		buf[len-1]=0;
		
		val=strchr(buf,'=');
		comment=strchr(buf,';');
		if(val && (!comment || comment>val)) {
			if(comment) {
				*comment=0;
			}
			end=val;
			*val=0;
			val++;
			while(isspace(*val) && *val != 0) {
				val++;
			}
			end--;
			while(isspace(*end) && end != buf) {end--;}
			end++;
			*end=0;
			if(strcmp(buf,"dbhost")==0) {
				ast_verbose(VERBOSE_PREFIX_3 "  |-> Option '%s' found with value '%s'\n",buf,val);
				strncpy(c->dbhost,val,32);
			} else if(strcmp(buf,"dbname")==0) {
				ast_verbose(VERBOSE_PREFIX_3 "  |-> Option '%s' found with value '%s'\n",buf,val);
				strncpy(c->dbname,val,32);
			} else if(strcmp(buf,"dbuser")==0) {
				ast_verbose(VERBOSE_PREFIX_3 "  |-> Option '%s' found with value '%s'\n",buf,val);
				strncpy(c->dbuser,val,32);
			} else if(strcmp(buf,"dbpass")==0) {
				ast_verbose(VERBOSE_PREFIX_3 "  |-> Option '%s' found with value '%s'\n",buf,val);
				strncpy(c->dbpass,val,32);
			} else if(strcmp(buf,"enablelcr")==0) {
				ast_verbose(VERBOSE_PREFIX_3 "  |-> Option '%s' found with value '%s'\n",buf,val);
				strncpy(c->enablelcr,val,32);
			} else {
				ast_verbose(VERBOSE_PREFIX_3 "  |-> I don't know how to handle key '%s' :(\n",buf);
			}
		} else {
			ast_verbose(VERBOSE_PREFIX_3 "  |-> Seems like a crap\n");
		}
	}
	ast_verbose(VERBOSE_PREFIX_3 "  +-> Config processed!\n");
	return 0;
}



static int astpp_lcr_exec(struct ast_channel *chan, void *data) {
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_lcr_exec() invoked\n");
	int len;
	char sqlcmd[1024],*buf;
	struct localuser *u;
	//struct astpp_conf c;
	len=strlen(data);
	if(len==0) {
		ast_log(LOG_ERROR, "ERROR: can't call '%s' without a param!\n",app1);
		return -1;
	}
	buf = (char*)malloc(2*len+1);
	MYSQL mysql;
	LOCAL_USER_ADD(u);
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_lcr_exec() configured\n");
	//astpp_load_config("/var/lib/astpp/astpp-config.conf",&c);
	
	mysql_init(&mysql);
	if (mysql_real_connect(&mysql, c.dbhost, c.dbuser, c.dbpass, c.dbname, 0, NULL, 0)) {
		ast_verbose(VERBOSE_PREFIX_3 "--> Connected to Database.\n");
	} else {
		ast_log(LOG_ERROR, "ERROR: cannot connect to database server %s.\n", c.dbhost);
		ast_log(LOG_ERROR, "ERROR: mysql err (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		LOCAL_USER_REMOVE(u);
		return -1;
	}
	mysql_real_escape_string(&mysql, buf, data, len);
	sprintf(sqlcmd,
		"SELECT o.pattern,o.cost,o.prepend,t.tech,t.path FROM outbound_routes o, trunks t WHERE '%s' RLIKE o.pattern AND o.status = 1 and o.trunk=t.name ORDER by LENGTH(o.pattern) DESC, o.cost",
		buf
	);
	free(buf);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);
	
	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		LOCAL_USER_REMOVE(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	MYSQL_RES *mysql_res=mysql_use_result(&mysql);
	MYSQL_ROW mysql_row;
	int i=1;
	while((mysql_row=mysql_fetch_row(mysql_res)) && i<=ASTPP_MAX_VARS) {
		ast_verbose(VERBOSE_PREFIX_3 "New record fetched from database. Processing...\n");
		ast_verbose(VERBOSE_PREFIX_3 "Result is: '%s','%s','%s','%s','%s'..\n",mysql_row[0],mysql_row[1],mysql_row[2],mysql_row[3],mysql_row[4]);
		char tmp[1024];
		tmp[0]=0; //trunking string
		ast_verbose(VERBOSE_PREFIX_3 "Comparing techs...\n");
		if(strcmp(mysql_row[3],"Local")==0) {
			sprintf(tmp,"Local/%s%s@%s/n",mysql_row[2],data,mysql_row[4]);
		} else if(strcmp(mysql_row[3],"IAX2")==0) {
			sprintf(tmp,"IAX2/%s/%s%s",mysql_row[4],mysql_row[2],data);
		} else if(strcmp(mysql_row[3],"Zap")==0) {
			sprintf(tmp,"Zap/%s/%s%s",mysql_row[4],mysql_row[2],data);
		} else if(strcmp(mysql_row[3],"SIP")==0) {
			sprintf(tmp,"SIP/%s/%s%s",mysql_row[4],mysql_row[2],data);
		}
		ast_verbose(VERBOSE_PREFIX_3 "Compare finished.\n");
		if(strlen(tmp)>0) {
			char varname[16];
			sprintf(varname,"LCRSTRING%d",i);
			ast_verbose(VERBOSE_PREFIX_3 "Seting variable '%s' to '%s'\n",varname,tmp);
			pbx_builtin_setvar_helper(chan,varname,tmp);
			i++;
		} else {
			ast_log(LOG_WARNING,"I don't know such tech, so i won't set LCRSTRING for it!\n");
		}
	}
	

	mysql_close(&mysql);
	LOCAL_USER_REMOVE(u);
	return 0;
}


static int astpp_auth_exec(struct ast_channel *chan, void *data) {
	struct localuser *u;
	int cardno,len;
	MYSQL mysql;
	char *args[2],sqlcmd[1024],*buf;
	if(ast_strlen_zero(data)) {
		ast_log(LOG_ERROR, "ERROR: can't call '%s' without a params!\n",app2);
		return -1;
	}
	LOCAL_USER_ADD(u);
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_auth_exec() invoked\n");
	ast_app_separate_args(data, '|', args, 2);
	ast_verbose(VERBOSE_PREFIX_3 "--> CARD: '%s', PHONE: '%s'\n",args[0],args[1]);
	// Connecting to database
	mysql_init(&mysql);
	if (mysql_real_connect(&mysql, c.dbhost, c.dbuser, c.dbpass, c.dbname, 0, NULL, 0)) {
		ast_verbose(VERBOSE_PREFIX_3 "--> Connected to Database.\n");
	} else {
		ast_log(LOG_ERROR, "ERROR: cannot connect to database server %s.\n", c.dbhost);
		ast_log(LOG_ERROR, "ERROR: mysql err (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		LOCAL_USER_REMOVE(u);
		return -1;
	}
	
	// Here we'll perfom: $carddata = &getcard($cardno);
	len=strlen(args[0]);
	buf = (char*)malloc(2*len+1);
	mysql_real_escape_string(&mysql, buf, args[0], len);

	sprintf(sqlcmd,
		"SELECT c.number,c.credit_limit,c.balance,b.markup FROM cards c,brands b WHERE c.number='%s' AND c.status=1 AND c.brand=b.name limit 1",
		buf
	);
	//free(buf);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);

	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		free(buf);
		LOCAL_USER_REMOVE(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	MYSQL_RES *mysql_res=mysql_use_result(&mysql);
	MYSQL_ROW mysql_card=mysql_fetch_row(mysql_res);
	if(!mysql_card) {
		ast_verbose(VERBOSE_PREFIX_3 "--> CARD with number '%s' - not found.\n",args[0]);
		ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=2\n");
		pbx_builtin_setvar_helper(chan,"CALLSTATUS","2");
		free(buf);
		LOCAL_USER_REMOVE(u);
		return 0;
	}
	mysql_free_result(mysql_res);
	// Balance is in mysql_card[2]
	int credit_limit = atoi(mysql_card[1])*10000;
	ast_verbose(VERBOSE_PREFIX_3 "--> Credit limit: '%d'\n",credit_limit);

	// Calculating balance for card:
	sprintf(sqlcmd,
		"SELECT SUM(debit)-SUM(credit) FROM cdrs WHERE cardnum='%s' and status NOT IN (1, 2)",
		buf
	);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);
	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		free(buf);
		LOCAL_USER_REMOVE(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	free(buf);
	mysql_res=mysql_use_result(&mysql);
	MYSQL_ROW mysql_saldo=mysql_fetch_row(mysql_res);
	int saldo=0;
	ast_verbose(VERBOSE_PREFIX_3 "--> Checking MySQL result...\n");
	if(!mysql_saldo[0]) {
		ast_verbose(VERBOSE_PREFIX_3 "--> NULL value fetched.\n");
		//LOCAL_USER_REMOVE(u);
		//return -1;
		
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "--> Saldo fetched from database: '%s'\n",mysql_saldo[0]);
		saldo=atoi(mysql_saldo[0]);
	}
	mysql_free_result(mysql_res);
	// Calculating balance
	int balance = saldo + atoi(mysql_card[2]);
	ast_verbose(VERBOSE_PREFIX_3 "--> Balance: '%d'\n",balance);

	// Calculating credit
	int credit = credit_limit - balance;
	ast_verbose(VERBOSE_PREFIX_3 "--> Credit remaining is: '%d'\n",credit);
	

	// Checking for number: (sub getphone())
	

	len=strlen(args[1]);
	buf = (char*)malloc(2*len+1);
	mysql_real_escape_string(&mysql, buf, args[1], len);

	sprintf(sqlcmd,
		"SELECT * FROM routes WHERE '%s' RLIKE pattern ORDER BY LENGTH(pattern) DESC limit 1",
		buf
	);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);
	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		free(buf);
		LOCAL_USER_REMOVE(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	mysql_res=mysql_use_result(&mysql);
	MYSQL_ROW mysql_number=mysql_fetch_row(mysql_res);
	if(!mysql_number) {
		ast_verbose(VERBOSE_PREFIX_3 "--> INVALID PHONE NUMBER: '%s'\n",args[1]);
		ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=1\n");
		pbx_builtin_setvar_helper(chan,"CALLSTATUS","1");
		free(buf);
		LOCAL_USER_REMOVE(u);
		return 0;
	}
	mysql_free_result(mysql_res);
	ast_verbose(VERBOSE_PREFIX_3 "--> Matching pattern is '%s'\n",args[1]);
	// $numdata->{cost} == mysql_number[4]
	// $numdata->{connectcost} == mysql_number[2]
	// $numdata->{includedseconds} == mysql_number[3]
	// $carddata->{markup} == mysql_card[3]
	// 
	int markup=0,cost=0,connectcost=0,incsecs=0,incmins=0,num_incsecs=0;
	if(mysql_number[4]!=NULL) {
		cost=atoi(mysql_number[4]);
	}
	if(mysql_number[3]!=NULL) {
		num_incsecs=atoi(mysql_number[3]);
	}
	if(mysql_number[2]!=NULL) {
		connectcost=atoi(mysql_number[2]);
	}
	if(mysql_card[3]!=NULL) {
		markup=atoi(mysql_card[3]);
	}
	
	ast_verbose(VERBOSE_PREFIX_3 "--> seconds included for this number '%d'\n",num_incsecs);
	ast_verbose(VERBOSE_PREFIX_3 "--> Calculating adjcost and adjconn...\n");
	int adjcost = (  cost * ( 10000 + markup ) ) / 10000;
	ast_verbose(VERBOSE_PREFIX_3 "--> adjcost is '%d'\n",adjcost);
	int adjconn = ( connectcost * ( 10000 + markup ) ) / 10000;
	ast_verbose(VERBOSE_PREFIX_3 "--> adjconn is '%d'\n",adjconn);

	if(adjconn>0) {
		incsecs=num_incsecs % 60;
		incmins=num_incsecs / 60;
		ast_verbose(VERBOSE_PREFIX_3 "--> Included:  '%d' minutes and '%d' seconds\n",incmins,incsecs);
	}
	
	int maxmins = (credit - adjconn)/adjcost;
	ast_verbose(VERBOSE_PREFIX_3 "--> Max length is '%d' minutes\n",maxmins);
	
	if(adjconn>credit || maxmins<=1) {
		ast_log(LOG_ERROR,"Credit (%d) is low! (adjconn: %d)\n", credit,adjconn);
		ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=0\n");
		pbx_builtin_setvar_helper(chan,"CALLSTATUS","0");
		LOCAL_USER_REMOVE(u);
		return 0;
	}

	if(maxmins>200000) {
		ast_verbose(VERBOSE_PREFIX_3 "--> Maxmins is too high! setting it to 200000\n");
		maxmins=200000;
	}

	// TIMELIMIT init
	sprintf(sqlcmd,
		"/n\|30\|HL(%d:60000:30000)",
		(maxmins * 60 * 1000)
	);
	ast_verbose(VERBOSE_PREFIX_3 "--> TIMELIMIT=%s\n",sqlcmd);
	pbx_builtin_setvar_helper(chan,"TIMELIMIT",sqlcmd);
	ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=3\n");
	pbx_builtin_setvar_helper(chan,"CALLSTATUS","3");

	
	
	// LCR part will be perfomed if it was enabled in config
	if(strcmp(c.enablelcr,"YES")==0) {
		ast_verbose(VERBOSE_PREFIX_3 "--> LCR Enabled. Calling astpp_lcr_exec()\n");
		astpp_lcr_exec(chan,args[1]);
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "--> LCR Disabled.\n");
	}
	free(buf);
	LOCAL_USER_REMOVE(u);
	return 0;
}




int unload_module(void) {
	STANDARD_HANGUP_LOCALUSERS;
	ast_unregister_application(app2);
	return ast_unregister_application(app1);
}


int load_module(void) {
	astpp_load_config(ASTPP_CONFIG_FILE,&c);
	
	ast_register_application(app2, astpp_auth_exec, synopsis2, descrip2);
	return ast_register_application(app1, astpp_lcr_exec, synopsis1, descrip1);
}

int reload_module(void) {
	unload_module();
	return load_module();
}


char *description(void) {
	return tdesc;
}


int usecount(void) {
	int res;
	STANDARD_USECOUNT(res);
	return res;
}


char *key() {
	return ASTERISK_GPL_KEY;
}