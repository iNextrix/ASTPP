/*
 * ASTPP - Open Source Voip Billing
 *
 * Copyright (C) 2004, Aleph Communications
 * 
 * ASTPP Team <info@astpp.org>
 * 
 * This program is Free Software and is distributed under the
 * terms of the GNU General Public License version 2.
 * 
 * This is an LCR and credit authorizing module for Asterisk(TM)
 * and ASTPP.   (www.astpp.org)
 * 
 * 
*/
#include "asterisk.h"

ASTERISK_FILE_VERSION(__FILE__, "$Revision: 2005 $")

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

#include "asterisk/app.h"

/* Comment this line to make this application compatible for Asterisk version 1.4.x */
#define ASTERISK_12	1

#define ASTPP_CONFIG_FILE "astpp-config.conf"

/* ASTPP_MAX_VARS allows you to limit maximum umber of variables set up by ASTPP_LCR
 * Lower number of routes will speedup work on big tables
 */
#define ASTPP_MAX_VARS 16

AST_MUTEX_DEFINE_STATIC(loglock);

struct astpp_conf {
	char dbhost[MAXHOSTNAMELEN];
	char dbname[32];
	char dbuser[32];
	char dbpass[32];
	int enablelcr;
} c;

static char *astpp_lcr_app = "ASTPP_LCR";
static char *astpp_lcr_synopsis = "ASTPP Low Cost Routing";
static char *astpp_lcr_descrip =
" Searches for Low Cost Routes in ASTPP database\n"
" Each found route placed into Variable, LCRSTRING1..LCRSTRINGN\n"
" Best route in LCRSTRING1, worst in LCRSTRINGN\n\n\n"
" [astpp-lcr]\n\n"
" exten => _1XXXXXXXXXX,1,ASTPP_LCR(${EXTEN}) \n"
" exten => _1XXXXXXXXXX,2,Set(GROUP(${TRUNK1}-OUTBOUND)=OUTBOUND) \n"
" exten => _1XXXXXXXXXX,3,GotoIf($[\"${GROUP_COUNT(OUTBOUND@${TRUNK1}-OUTBOUND)}\" > \"${TRUNK1_MAXCHANNELS}\"]?5) \n"
" exten => _1XXXXXXXXXX,4,Dial(${LCRSTRING1}||${TIMELIMIT}|${OPTIONS}) \n"
" exten => _1XXXXXXXXXX,105,Busy() \n"
" exten => _1XXXXXXXXXX,5,Set(GROUP(${TRUNK2}-OUTBOUND)=OUTBOUND) \n"
" exten => _1XXXXXXXXXX,6,GotoIf($[\"${GROUP_COUNT(OUTBOUND@${TRUNK2}-OUTBOUND)}\" > \"${TRUNK2_MAXCHANNELS}\"]?8) \n"
" exten => _1XXXXXXXXXX,7,Dial(${LCRSTRING2}||${TIMELIMIT}|${OPTIONS}) \n"
" exten => _1XXXXXXXXXX,108,Busy() \n"
" exten => _1XXXXXXXXXX,8,Set(GROUP(${TRUNK2}-OUTBOUND)=OUTBOUND) \n"
" exten => _1XXXXXXXXXX,9,GotoIf($[\"${GROUP_COUNT(OUTBOUND@${TRUNK3}-OUTBOUND)}\" > \"${TRUNK3_MAXCHANNELS}\"]?11) \n"
" exten => _1XXXXXXXXXX,10,Dial(${LCRSTRING3}||${TIMELIMIT}|${OPTIONS}) \n"
" exten => _1XXXXXXXXXX,111,Busy() \n"
" exten => _1XXXXXXXXXX,11,Set(GROUP(${TRUNK4}-OUTBOUND)=OUTBOUND) \n"
" exten => _1XXXXXXXXXX,12,GotoIf($[\"${GROUP_COUNT(OUTBOUND@${TRUNK4}-OUTBOUND)}\" > \"${TRUNK4_MAXCHANNELS}\"]?14) \n"
" exten => _1XXXXXXXXXX,13,Dial(${LCRSTRING4}||${TIMELIMIT}|${OPTIONS}) \n"
" exten => _1XXXXXXXXXX,114,Busy() \n"
" exten => _1XXXXXXXXXX,14,Set(GROUP(${TRUNK5}-OUTBOUND)=OUTBOUND) \n"
" exten => _1XXXXXXXXXX,15,GotoIf($[\"${GROUP_COUNT(OUTBOUND@${TRUNK5})-OUTBOUND}\" > \"${TRUNK5_MAXCHANNELS}\"]?17) \n"
" exten => _1XXXXXXXXXX,16,Dial(${LCRSTRING5}||${TIMELIMIT}|${OPTIONS}) \n"
" exten => _1XXXXXXXXXX,117,Busy() \n"
" exten => _1XXXXXXXXXX,17,Congestion() \n";


static char *astpp_auth_app = "ASTPP_AUTH";
static char *astpp_auth_synopsis = "ASTPP Card Auth";
static char *astpp_auth_descrip =
" Card-number and number to dial derived from command-line.\n"
" Call script with the card-number as first arg and the number\n"
" to dial as the second arg.  astpp-authorize will return a line containing info\n"
" that will cut the call off before it goes over the users credit limit.  The\n"
" user can get over credit limit if they have multiple calls going at once.\n"
" Presently the only way to stop that is to limit them to one call which is not\n"
" a nice solution.\n\n"
"\n"
//" The following variables must be set:\n"
//"  USER1 = Username at IAX2 Provider #1\n"
//"  PASS1 = Password for User1\n"
//"  PROVIDER1 = IP address of IAX2 Provider #1 or else associate name from\n"
//"     iax.conf\n"
//"\n"
//" With LCR\n\n\n"
" [astpp]\n\n"
" exten => _1XXXXXXXXXX,1,ASTPP_AUTH(${CARD}|${EXTEN})\n"
" exten => _1XXXXXXXXXX,2,GotoIf($[\"${CALLSTATUS}\" = \"0\"]?60)\n"
" exten => _1XXXXXXXXXX,3,GotoIf($[\"${CALLSTATUS}\" = \"1\"]?70)\n"
" exten => _1XXXXXXXXXX,4,GotoIf($[\"${CALLSTATUS}\" = \"2\"]?80)\n"
" exten => _1XXXXXXXXXX,5,Dial(${LCRSTRING1}||${TIMELIMIT}|${OPTIONS})\n"
" exten => _1XXXXXXXXXX,106,Busy()\n"
" exten => _1XXXXXXXXXX,6,Dial(${LCRSTRING2}||${TIMELIMIT}|${OPTIONS})\n"
" exten => _1XXXXXXXXXX,107,Busy()\n"
" exten => _1XXXXXXXXXX,7,Dial(${LCRSTRING3}||${TIMELIMIT}|${OPTIONS})\n"
" exten => _1XXXXXXXXXX,108,Busy()\n"
" exten => _1XXXXXXXXXX,8,Dial(${LCRSTRING4}||${TIMELIMIT}|${OPTIONS})\n"
" exten => _1XXXXXXXXXX,109,Busy()\n"
" exten => _1XXXXXXXXXX,9,Dial(${LCRSTRING5}||${TIMELIMIT}|${OPTIONS})\n"
" exten => _1XXXXXXXXXX,110,Busy()\n"
" exten => _1XXXXXXXXXX,60,Congestion() ; '0' Tells them they do not have enough money\n"
" exten => _1XXXXXXXXXX,61,Hangup()\n"
" exten => _1XXXXXXXXXX,70,Congestion '1' Bad Phone Number\n"
" exten => _1XXXXXXXXXX,71,Hangup()\n"
" exten => _1XXXXXXXXXX,80,Congestion()\n"
" exten => _1XXXXXXXXXX,81,Hangup()\n"
"; This lines are optional and would forward users to a help desk if the call did not go through.\n"
" exten => _1XXXXXXXXXX,60,Dial(SIP/HELPDESK) ; '0' Tells them they do not have enough money\n"
" exten => _1XXXXXXXXXX,61,Hangup()\n";

/* Compatibility Stuff */
#ifdef ASTERISK_12

#define USER		localuser
#define USERADD(u)	LOCAL_USER_ADD(u)
#define USERDEL(u)	LOCAL_USER_REMOVE(u)
#define	USERHANGUP	STANDARD_HANGUP_LOCALUSERS

STANDARD_LOCAL_USER;
LOCAL_USER_DECL;

static char *tdesc = "ASTPP binary functions: ASTPP_LCR(),ASTPP_AUTH()";

#else

#define USER		ast_module_user
#define USERADD(u)	u=ast_module_user_add(chan)
#define USERDEL(u)	ast_module_user_remove(u)
#define	USERHANGUP	ast_module_user_hangup_all()

#endif

static int astpp_load_config(const char *filename, struct astpp_conf *c) {
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_load_config() invoked\n");
	
	const char *s;
	struct ast_config *cfg;
	cfg = ast_config_load(filename);

	if(!cfg) {
		ast_log(LOG_ERROR,"ERROR: Can't open file '%s'.\n",filename);
		return 0;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "  |-> file opened '%s'\n",filename);
	}

	ast_mutex_lock(&loglock);
	
	if ((s = ast_variable_retrieve(cfg, "general", "dbhost"))) {
		ast_copy_string(c->dbhost, s, sizeof(c->dbhost));
		ast_verbose(VERBOSE_PREFIX_3 "  |-> Option 'dbhost' found with value '%s'\n", s);
	} else {
		ast_log(LOG_WARNING, "MySQL host not specified. Assuming 'localhost'.\n");
		ast_copy_string(c->dbhost, "localhost", sizeof(c->dbhost));
	}
	
	if ((s = ast_variable_retrieve(cfg, "general", "dbname"))) {
		ast_copy_string(c->dbname, s, sizeof(c->dbname));
		ast_verbose(VERBOSE_PREFIX_3 "  |-> Option 'dbname' found with value '%s'\n", s);
	} else {
		ast_log(LOG_WARNING, "MySQL database name not specified. Assuming 'astpp'.\n");
		ast_copy_string(c->dbname, "astpp", sizeof(c->dbname));
	}

	if ((s = ast_variable_retrieve(cfg, "general", "dbuser"))) {
		ast_copy_string(c->dbuser, s, sizeof(c->dbuser));
		ast_verbose(VERBOSE_PREFIX_3 "  |-> Option 'dbuser' found with value '%s'\n", s);
	} else {
		ast_log(LOG_WARNING, "MySQL database username not specified. Assuming 'root'.\n");
		ast_copy_string(c->dbuser, "root", sizeof(c->dbuser));
	}

	if ((s = ast_variable_retrieve(cfg, "general", "dbpass"))) {
		ast_copy_string(c->dbpass, s, sizeof(c->dbpass));
		ast_verbose(VERBOSE_PREFIX_3 "  |-> Option 'dbpass' found with value '%s'\n", s);
	} else {
		ast_log(LOG_WARNING, "MySQL user password not specified. Assuming blank.\n");
		ast_copy_string(c->dbpass, "", sizeof(c->dbpass));
	}

	if ((s = ast_variable_retrieve(cfg, "general", "enablelcr"))) {
		sscanf(s, "%d", &c->enablelcr);
		ast_verbose(VERBOSE_PREFIX_3 "  |-> Option 'enablelcr' found with value '%s'\n", s);
	} else {
		ast_log(LOG_WARNING, "Option 'enablelcr' not specified. Assuming 0.\n");
		c->enablelcr = 0;
	}
	
	ast_verbose(VERBOSE_PREFIX_3 "  +-> Config processed!\n");
	ast_mutex_unlock(&loglock);
	return 0;
}

static int astpp_mysql_connect(const struct astpp_conf *c, MYSQL *mysql) {
	mysql_init(mysql);
	
	if (mysql_real_connect(mysql, c->dbhost, c->dbuser, c->dbpass, c->dbname, 0, NULL, 0)) {
		ast_verbose(VERBOSE_PREFIX_3 "--> Connected to Database.\n");
		return 1;
	} else {
		ast_log(LOG_ERROR, "ERROR: cannot connect to database server %s.\n", c->dbhost);
		ast_log(LOG_ERROR, "MySQL ERROR (%d): %s\n", mysql_errno(mysql), mysql_error(mysql));
	}
	
	return 0;
}

static int astpp_lcr_exec(struct ast_channel *chan, void *data) {
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_lcr_exec() invoked\n");
	
	MYSQL mysql;
	char sqlcmd[1024],*buf;
	struct USER *u;
	int len = strlen(data);
	
	if(len == 0) {
		ast_log(LOG_ERROR, "ERROR: can't call '%s' without a param!\n",astpp_lcr_app);
		return -1;
	}
	
	buf = (char*)malloc(2*len+1);
	USERADD(u);
	
	// Connecting to database
	if (!astpp_mysql_connect(&c, &mysql)) {
		USERDEL(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "--> astpp_lcr_exec() configured\n");
	}
	
	mysql_real_escape_string(&mysql, buf, data, len);
	sprintf(sqlcmd,	"SELECT o.pattern,o.cost,o.prepend,t.tech,t.path FROM outbound_routes o, trunks t WHERE '%s' RLIKE o.pattern AND o.status = 1 and o.trunk=t.name ORDER by LENGTH(o.pattern) DESC, o.cost", buf);
	free(buf);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);
	
	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		USERDEL(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	
	int i = 1;
	MYSQL_RES *mysql_res = mysql_use_result(&mysql);
	MYSQL_ROW mysql_row;
	
	while((mysql_row=mysql_fetch_row(mysql_res)) && i<=ASTPP_MAX_VARS) {
		ast_verbose(VERBOSE_PREFIX_3 "New record fetched from database. Processing...\n");
		ast_verbose(VERBOSE_PREFIX_3 "Result is: '%s','%s','%s','%s','%s'..\n",mysql_row[0],mysql_row[1],mysql_row[2],mysql_row[3],mysql_row[4]);
		char tmp[1024];
		tmp[0] = 0; //trunking string
		ast_verbose(VERBOSE_PREFIX_3 "Comparing techs...\n");
		if(strcmp(mysql_row[3],"Local")==0) {
			sprintf(tmp,"Local/%s%s@%s/n",mysql_row[2],(char*)data,mysql_row[4]);
		} else if(strcmp(mysql_row[3],"IAX2")==0) {
			sprintf(tmp,"IAX2/%s/%s%s",mysql_row[4],mysql_row[2],(char*)data);
		} else if(strcmp(mysql_row[3],"Zap")==0) {
			sprintf(tmp,"Zap/%s/%s%s",mysql_row[4],mysql_row[2],(char*)data);
		} else if(strcmp(mysql_row[3],"SIP")==0) {
			sprintf(tmp,"SIP/%s/%s%s",mysql_row[4],mysql_row[2],(char*)data);
		}
		ast_verbose(VERBOSE_PREFIX_3 "Compare finished.\n");
		if(strlen(tmp) > 0) {
			char varname[16];
			sprintf(varname,"LCRSTRING%d",i);
			ast_verbose(VERBOSE_PREFIX_3 "Setting variable '%s' to '%s'\n",varname,tmp);
			pbx_builtin_setvar_helper(chan,varname,tmp);
			i++;
		} else {
			ast_log(LOG_WARNING,"I don't know such tech, so i won't set LCRSTRING for it!\n");
		}
	}

	mysql_close(&mysql);
	USERDEL(u);
	return 0;
}

static int astpp_auth_exec(struct ast_channel *chan, void *data) {
	MYSQL mysql;
	char *args[2],sqlcmd[1024],*buf;
	struct USER *u;
	
	if(ast_strlen_zero(data)) {
		ast_log(LOG_ERROR, "ERROR: can't call '%s' without a params!\n",astpp_auth_app);
		return -1;
	}
	
	USERADD(u);
	ast_verbose(VERBOSE_PREFIX_3 "--> astpp_auth_exec() invoked\n");
	ast_app_separate_args(data, '|', args, 2);
	ast_verbose(VERBOSE_PREFIX_3 "--> CARD: '%s', PHONE: '%s'\n",args[0],args[1]);
	
	// Connecting to database
	if (!astpp_mysql_connect(&c, &mysql)) {
		USERDEL(u);
		return -1;
	}
	
	// Here we'll perfom: $carddata = &getcard($cardno);
	int len = strlen(args[0]);
	buf = (char*)malloc(2*len+1);
	
	mysql_real_escape_string(&mysql, buf, args[0], len);
	sprintf(sqlcmd,	"SELECT c.number,c.credit_limit,c.balance,b.markup FROM accounts c,pricelists b WHERE c.number='%s' AND c.status=1 AND c.pricelist=b.name limit 1", buf);
	
	//free(buf);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);

	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));

		free(buf);
		USERDEL(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	
	MYSQL_RES *mysql_res = mysql_use_result(&mysql);
	MYSQL_ROW mysql_card = mysql_fetch_row(mysql_res);
	
	if(!mysql_card) {
		ast_verbose(VERBOSE_PREFIX_3 "--> CARD with number '%s' - not found.\n",args[0]);
		ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS = 2\n");
		pbx_builtin_setvar_helper(chan,"CALLSTATUS","2");

		free(buf);
		USERDEL(u);
		return 0;
	}
	
	mysql_free_result(mysql_res);
	
	// Balance is in mysql_card[2]
	int credit_limit = atoi(mysql_card[1])*10000;
	ast_verbose(VERBOSE_PREFIX_3 "--> Credit limit: '%d'\n",credit_limit);

	// Calculating balance for card:
	sprintf(sqlcmd,	"SELECT SUM(debit)-SUM(credit) FROM cdrs WHERE cardnum='%s' and status NOT IN (1, 2)", buf);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);
	
	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		
		free(buf);
		USERDEL(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}

	free(buf);
	
	int saldo=0;
	mysql_res = mysql_use_result(&mysql);
	MYSQL_ROW mysql_saldo = mysql_fetch_row(mysql_res);
	ast_verbose(VERBOSE_PREFIX_3 "--> Checking MySQL result...\n");
	
	if(!mysql_saldo[0]) {
		ast_verbose(VERBOSE_PREFIX_3 "--> NULL value fetched.\n");
		
		free(buf);
		USERDEL(u);
		//return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "--> Saldo fetched from database: '%s'\n",mysql_saldo[0]);
		saldo = atoi(mysql_saldo[0]);
	}
	
	mysql_free_result(mysql_res);
	
	// Calculating balance
	int balance = saldo + atoi(mysql_card[2]);
	ast_verbose(VERBOSE_PREFIX_3 "--> Balance: '%d'\n",balance);

	// Calculating credit
	int credit = credit_limit - balance;
	ast_verbose(VERBOSE_PREFIX_3 "--> Credit remaining is: '%d'\n",credit);
	
	// Checking for number: (sub getphone())
	len = strlen(args[1]);
	buf = (char*)malloc(2*len+1);
	mysql_real_escape_string(&mysql, buf, args[1], len);

	sprintf(sqlcmd,	"SELECT * FROM routes WHERE '%s' RLIKE pattern ORDER BY LENGTH(pattern) DESC limit 1", buf);
	ast_verbose(VERBOSE_PREFIX_3 "--> SQL: '%s'\n",sqlcmd);
	if(mysql_real_query(&mysql, sqlcmd, strlen(sqlcmd))) {
		ast_log(LOG_ERROR,"SELECT ERROR: (%d) %s\n", mysql_errno(&mysql), mysql_error(&mysql));
		
		free(buf);
		USERDEL(u);
		return -1;
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "SELECT successfull.\n");
	}
	
	mysql_res = mysql_use_result(&mysql);
	MYSQL_ROW mysql_number = mysql_fetch_row(mysql_res);
	
	if(!mysql_number) {
		ast_verbose(VERBOSE_PREFIX_3 "--> INVALID PHONE NUMBER: '%s'\n",args[1]);
		ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=1\n");
		pbx_builtin_setvar_helper(chan,"CALLSTATUS","1");
		
		free(buf);
		USERDEL(u);
		return 0;
	}
	
	pbx_builtin_setvar_helper(chan,"OPTIONS","Hj");
	mysql_free_result(mysql_res);
	
	ast_verbose(VERBOSE_PREFIX_3 "--> Matching pattern is '%s'\n",args[1]);
	// $numdata->{cost} == mysql_number[4]
	// $numdata->{connectcost} == mysql_number[2]
	// $numdata->{includedseconds} == mysql_number[3]
	// $carddata->{markup} == mysql_card[3]
	// 
	int markup = 0, cost = 0, connectcost = 0, incsecs = 0, incmins = 0, num_incsecs = 0;
	
	if(mysql_number[4] != NULL) {
		cost=atoi(mysql_number[4]);
	}
	
	if(mysql_number[3] != NULL) {
		num_incsecs = atoi(mysql_number[3]);
	}
	
	if(mysql_number[2] != NULL) {
		connectcost = atoi(mysql_number[2]);
	}
	
	if(mysql_card[3] != NULL) {
		markup = atoi(mysql_card[3]);
	}
	
	ast_verbose(VERBOSE_PREFIX_3 "--> seconds included for this number '%d'\n",num_incsecs);
	ast_verbose(VERBOSE_PREFIX_3 "--> Calculating adjcost and adjconn...\n");
	
	int adjcost = (  cost * ( 10000 + markup ) ) / 10000;
	ast_verbose(VERBOSE_PREFIX_3 "--> adjcost is '%d'\n",adjcost);
	
	int adjconn = ( connectcost * ( 10000 + markup ) ) / 10000;
	ast_verbose(VERBOSE_PREFIX_3 "--> adjconn is '%d'\n",adjconn);

	if(adjconn > 0) {
		incsecs=num_incsecs % 60;
		incmins=num_incsecs / 60;
		ast_verbose(VERBOSE_PREFIX_3 "--> Included:  '%d' minutes and '%d' seconds\n",incmins,incsecs);
	}
	
	int maxmins = (credit - adjconn) / adjcost;
	ast_verbose(VERBOSE_PREFIX_3 "--> Max length is '%d' minutes\n", maxmins);
	
	if(adjconn > credit || maxmins <= 1) {
		ast_log(LOG_ERROR,"Credit (%d) is low! (adjconn: %d)\n", credit,adjconn);
		ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=0\n");
		pbx_builtin_setvar_helper(chan,"CALLSTATUS","0");
		USERDEL(u);
		return 0;
	}

	if(maxmins>200000) {
		ast_verbose(VERBOSE_PREFIX_3 "--> Maxmins is too high! setting it to 200000\n");
		maxmins=200000;
	}

	// TIMELIMIT init
	sprintf(sqlcmd,	"/n\\|30\\|HL(%d:60000:30000)",	(maxmins * 60 * 1000));
	ast_verbose(VERBOSE_PREFIX_3 "--> TIMELIMIT=%s\n",sqlcmd);
	pbx_builtin_setvar_helper(chan,"TIMELIMIT",sqlcmd);
	ast_verbose(VERBOSE_PREFIX_3 "--> CALLSTATUS=3\n");
	pbx_builtin_setvar_helper(chan,"CALLSTATUS","3");
	
	// LCR part will be perfomed if it was enabled in config
	if(c.enablelcr) {
		ast_verbose(VERBOSE_PREFIX_3 "--> LCR Enabled. Calling astpp_lcr_exec()\n");
		astpp_lcr_exec(chan,args[1]);
	} else {
		ast_verbose(VERBOSE_PREFIX_3 "--> LCR Disabled.\n");
	}
	
	free(buf);
	USERDEL(u);
	
	return 0;
}

#ifdef ASTERISK_12
int unload_module(void) {
#else
static int unload_module(void) {
#endif
	int res;
	USERHANGUP;
	res  = ast_unregister_application(astpp_lcr_app);
	res |= ast_unregister_application(astpp_auth_app);
	return res;
}

#ifdef ASTERISK_12
int load_module(void) {
#else
static int load_module(void) {
#endif
	astpp_load_config(ASTPP_CONFIG_FILE, &c);
	
	ast_register_application(astpp_auth_app, astpp_auth_exec, astpp_auth_synopsis, astpp_auth_descrip);
	return ast_register_application(astpp_lcr_app, astpp_lcr_exec, astpp_lcr_synopsis, astpp_lcr_descrip);
}

#ifdef ASTERISK_12
/*
int reload_module(void) {
	unload_module();
	return load_module();
}
*/
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
#else
AST_MODULE_INFO_STANDARD(ASTERISK_GPL_KEY, "ASTPP binary functions: ASTPP_LCR(), ASTPP_AUTH()");
#endif
