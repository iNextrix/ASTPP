/*
 * ASTPP - Open Source Voip Billing
 *
 * Copyright (C) 2004, Aleph Communications
 * 
 * ASTPP Team <info@astpp.org>
 * 
 * This program is Free Software and is distributed under the
 * terms of the GNU General Public License version 2.
 */

#include <stdio.h> 
#include <sys/types.h>
#include <asterisk/channel.h>
#include <asterisk/cdr.h>
#include <asterisk/module.h>
#include <asterisk/logger.h>
#include <asterisk/cli.h>
#include <asterisk/config.h>
#include <asterisk.h>

#include <stdio.h>
#include <string.h>
#include <errno.h>

#include <stdlib.h>
#include <unistd.h>
#include <time.h>
#include <sys/file.h>

/* Comment this line if you are using Asterisk 1.4.x */
#define ASTERISK_12 1

#ifdef ASTERISK_12
#include <asterisk/options.h>
#endif

/* The values are as follows:


  "accountcode", 	// accountcode is the account name of detail records, Master.shell contains all records
  			// Detail records are configured on a channel basis, IAX and SIP are determined by user
			// Zap is determined by channel in zaptel.conf
  "source", 
  "destination", 
  "destination context", 
  "callerid", 
  "channel", 
  "destination channel", 	(if applicable)
  "last application", 	// Last application run on the channel
  "last app argument", 	// argument to the last channel
  "start time", 
  "answer time", 
  "end time", 
  duration,   		// Duration is the whole length that the entire call lasted. ie. call rx'd to hangup 
  			// "end time" minus "start time"
  billable seconds, 	// the duration that a call was up after other end answered which will be <= to duration 
  			// "end time" minus "answer time"
  "disposition",    	// ANSWERED, NO ANSWER, BUSY
  "amaflags",       	// DOCUMENTATION, BILL, IGNORE etc, specified on a per channel basis like accountcode.
  "uniqueid",           // unique call identifier
  "userfield"		// user field set via SetCDRUserField
*/


static int active = 0;
static char *name = "shell";

#define  CDR_STRING_SIZE 128
#define  MAX_REG 10
#define  CDR_ELEM 19

static char registry[MAX_REG][CDR_STRING_SIZE];
static const char *BLANK_STRING = {""};

static int is_executable(char *pathname)
{
	int x ;
	static struct stat buf ;

	if ( pathname == NULL || *pathname == '\0' ) { return 0; }
	x = stat( pathname , &buf );	   if ( x )  { return 0; }
	x = (buf.st_mode & S_IXOTH) != 0;  if ( x )  { return x; }
	x = ( getuid() == buf.st_uid && (buf.st_mode & S_IXUSR) != 0 );
	return x;
}

static int shell_log(struct ast_cdr *cdr) {
	struct timeval start;
	char buf[CDR_ELEM][CDR_STRING_SIZE];
	int x = 1, pid = 0;

	memset(buf, 0, sizeof(buf));

	/* Account code */
	snprintf(buf[1],CDR_STRING_SIZE, "%s", cdr->accountcode && strlen(cdr->accountcode) ? cdr->accountcode : BLANK_STRING);
	/* Source */
	snprintf(buf[2],CDR_STRING_SIZE, "%s", cdr->src && strlen(cdr->src) ? cdr->src : BLANK_STRING);
	/* Destination */
	snprintf(buf[3],CDR_STRING_SIZE, "%s", cdr->dst && strlen(cdr->dst) ? cdr->dst : BLANK_STRING);
	/* Destination context */
	snprintf(buf[4],CDR_STRING_SIZE, "%s", cdr->dcontext && strlen(cdr->dst) ? cdr->dst : BLANK_STRING);
	/* Caller*ID */
	snprintf(buf[5],CDR_STRING_SIZE, "%s", cdr->clid && strlen(cdr->clid) ? cdr->clid : BLANK_STRING);
	/* Channel */
	snprintf(buf[6],CDR_STRING_SIZE, "%s", cdr->channel && strlen(cdr->channel) ? cdr->channel : BLANK_STRING);
	/* Destination Channel */
	snprintf(buf[7],CDR_STRING_SIZE, "%s", cdr->dstchannel && strlen(cdr->dstchannel) ? cdr->dstchannel : BLANK_STRING);
	/* Last Application */
	snprintf(buf[8],CDR_STRING_SIZE, "%s", cdr->lastapp && strlen(cdr->lastapp) ? cdr->lastapp : BLANK_STRING);
	/* Last Data */
	snprintf(buf[9],CDR_STRING_SIZE, "%s", cdr->lastdata && strlen(cdr->lastdata) ? cdr->lastdata : BLANK_STRING);
	/* Start Time */
	snprintf(buf[10],CDR_STRING_SIZE, "%ld", cdr->start.tv_sec);
	/* Answer Time */
	snprintf(buf[11],CDR_STRING_SIZE, "%ld", cdr->answer.tv_sec);
	/* End Time */
	snprintf(buf[12],CDR_STRING_SIZE, "%ld", cdr->end.tv_sec);
	/* Duration */
	snprintf(buf[13],CDR_STRING_SIZE, "%ld", cdr->duration);
	/* Billable seconds */
	snprintf(buf[14],CDR_STRING_SIZE, "%ld", cdr->billsec);
	/* Disposition */
	snprintf(buf[15],CDR_STRING_SIZE, "%s", ast_cdr_disp2str(cdr->disposition));
	/* AMA Flags */
	snprintf(buf[16],CDR_STRING_SIZE, "%s", ast_cdr_flags2str(cdr->amaflags));
	/* Unique ID */
	snprintf(buf[17],CDR_STRING_SIZE, "%s", cdr->uniqueid && strlen(cdr->uniqueid) ? cdr->uniqueid : BLANK_STRING);
	/* append the user field */
	snprintf(buf[18],CDR_STRING_SIZE, "%s", cdr->userfield && strlen(cdr->userfield) ? cdr->userfield : BLANK_STRING);

	for (x=0; x<MAX_REG; x++) {
		if (!strlen(registry[x])) {
			break;
		}
		
		start = ast_tvnow();
		if (!(pid = fork())) {
			snprintf(buf[0],CDR_STRING_SIZE,registry[x]);
			if (is_executable(buf[0])) {
				int res = execl(buf[0],buf[0],buf[1],buf[2],buf[3],buf[4],
						buf[5],buf[6],buf[7],buf[8],
						buf[9],buf[10],buf[11],buf[12],
						buf[13],buf[14],buf[15],buf[16],
						buf[17],buf[18],NULL);
				if(res) {
					ast_log(LOG_ERROR, "Unable to execute '%s': %s\n", buf[0], strerror(errno));
					_exit(res);
				}
			} else {
				ast_log(LOG_ERROR, "File: '%s' is not executable, Ignored...\n", buf[0]);
			}
		}
		ast_verbose(VERBOSE_PREFIX_3 "Execution of '%s' took %d milliseconds\n", registry[x], ast_tvdiff_ms(ast_tvnow(), start));
	}

	return 0;
}

#ifdef ASTERISK_12
int unload_module(void)
#else
static int unload_module(void)
#endif
{
	ast_cdr_unregister(name);
	return 0;
}

#ifdef ASTERISK_12
static char *desc = "Shell CDR Backend";
int load_module(void)
#else
static int load_module(void)
#endif
{
	int res = -1;
	int x = 0;
	struct ast_config *cfg;
	struct ast_variable *var;
	
	if ((cfg = ast_config_load("cdr.conf"))) {
		for (var = ast_variable_browse(cfg, "cdr_shell"); var; var = var->next) {
			if (!strcasecmp(var->name, "path")) {
				if (is_executable(var->value)) {
					strncpy(registry[x],var->value,CDR_STRING_SIZE);
					registry[x+1][0] = '\0';
					ast_log(LOG_NOTICE, "Registered CDR process #%d %s\n", x+1,registry[x]);
					x++;
				}
				else {
					ast_log(LOG_ERROR, "File: '%s' is not executable, Ignored...\n", var->value);
				}
			}
			active = x;
		}
		ast_config_destroy(cfg);
	}

	if (active) {
#ifdef ASTERISK_12
		res = ast_cdr_register(name, desc, shell_log);
#else
		res = ast_cdr_register(name, ast_module_info->description, shell_log);
#endif
	}
	
	if (res) {
		ast_log(LOG_ERROR, "Unable to register SHELL CDR handling for %d shell scripts\n", active);
	}

	return res;
}

#ifdef ASTERISK_12
char *description(void)
{
	return desc;
}
int reload(void)
{
	return 0;
}

int usecount(void)
{
	return 0;
}

char *key()
{
	return ASTERISK_GPL_KEY;
}
#else
AST_MODULE_INFO_STANDARD(ASTERISK_GPL_KEY, "Shell CDR Backend");
#endif
