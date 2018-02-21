#Makefile for ASTPP (www.astpp.org)
SHELL=/bin/sh

#ASTPP Configuration
ASTPPDIR=/var/lib/astpp/
ASTPPEXECDIR=/usr/local/astpp/
ASTPPLOGDIR=/var/log/astpp/
LOCALE_DIR=/usr/local/share/locale

#Freeswich Configuration
FS_DIR=/usr/local/freeswitch
FS_SOUNDSDIR=${FS_DIR}/sounds/en/us/callie
FS_SCRIPTS=${FS_DIR}/scripts

#Asterisk Configuration
ASTERISKDIR=/etc/asterisk
AGIDIR=/var/lib/asterisk/agi-bin
SOUNDSDIR=/var/lib/asterisk/sounds

#Other Configuration
OWNER=root
GROUP=root
WWWDIR=/var/www
APACHE=/etc/httpd

all: install

#Perl package installation function
install_perl:	
	perl -MCPAN -e "install Bundle::CPAN,ExtUtils::CBuilder,Text::Template,Params::Check,Locale::gettext_pp,Locale::Country,Locale::Language,DBI,DBD::mysql,YAML,Params::Validate,CGI,Asterisk::AGI,LWP::Simple,URI::Escape,POE::Component::Client::Asterisk::Manager,Getopt::Long,Text::CSV,Mail::Sendmail,Email::Simple,Time::DaysInMonth,Data::Paginate,HTML::Template,HTML::Template::Expr,DateTime,DateTime::TimeZone,DateTime::Locale,XML::Simple,XML::LibXML,Module::Build,Class::Singleton,Data::Dumper,Module::Build,Class::Singleton,Text::Template,Locale::Country,Data::Dumper,IO::Tty,IO::All,Test::Pod,MIME::Types,POE::Test::Loops,Storable,Time::Zone,Date::Parse,Curses,IO::String,POE,Sys::Syslog,Log::Dispatch,Test::Simple,FCGI,Email::Date::Format,ExtUtils::CBuilder,Set::Infinite,DateTime::Set,DateTime::Event::Recurrence,DateTime::Incomplete,Class::Data::Inheritable,Locale::Country::Multilingual,File::Slurp,Template::Simple,Template::Plugin::DateTime,Date::Language,Date::Format,Text::CSV_XS,Term::ReadKey,DateTime::Format::Strptime,DBI::Shell,Net::Daemon,DBD::Mutiplex,TimeDate,Mail::Tools,Asterisk::Manager,Asterisk::Outgoing,Asterisk::Voicemail,Template::Toolkit,Date::Language,Date::Format,DateTime::Locale,XML::Writer";
	cd modules/ASTPP && perl Makefile.PL && make && make install && cd ../../

#ASTPP folder creation function
install_astpp_folders:
	mkdir -p $(DESTDIR)$(ASTPPDIR)
	mkdir -p $(DESTDIR)$(ASTPPLOGDIR)
	mkdir -p $(DESTDIR)$(ASTPPEXECDIR)	
	mkdir -p $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin	
	
	chown $(OWNER) $(DESTDIR)$(ASTPPDIR)
	chown $(OWNER) $(DESTDIR)$(ASTPPLOGDIR)
	chown $(OWNER) $(DESTDIR)$(ASTPPEXECDIR)
	chgrp $(GROUP) $(DESTDIR)$(ASTPPDIR)
	chgrp $(GROUP) $(DESTDIR)$(ASTPPLOGDIR)
	chgrp $(GROUP) $(DESTDIR)$(ASTPPEXECDIR)	

#ASTPP scripts installation function
install_astpp_scripts:
	for x in scripts/*.pl; do \
		echo $$x; \
		install -m 755 -o $(OWNER) -g $(GROUP) $$x $(PREFIX)$(ASTPPEXECDIR); \
	done

#ASTPP config file installation function	
install_astpp_config:
	install -m 644 -o $(OWNER) -g $(GROUP) astpp_confs/sample.astpp-config.conf $(DESTDIR)$(ASTPPDIR)/astpp-config.conf
	install -m 644 -o $(OWNER) -g $(GROUP) astpp_confs/sample.reseller-config.conf $(DESTDIR)$(ASTPPDIR)/sample.reseller-config.conf

#ASTPP GUI installation function
install_astpp_gui:
	mkdir -p $(DESTDIR)$(WWWDIR)/html/astpp
	chgrp $(GROUP) $(DESTDIR)$(WWWDIR)/html/astpp
	
	cp -Rf web_interface/astpp/* $(DESTDIR)$(WWWDIR)/html/astpp/
	install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp/htaccess $(DESTDIR)$(WWWDIR)/html/astpp/.htaccess
	install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-wraper.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin/astpp-wraper.cgi
	install -m 755 -o $(OWNER) -g $(GROUP) web_interface/apache/astpp.conf $(APACHE)/conf.d/astpp.conf

#Freeswitch scripts installation function
install_freeswitch_scripts:
	install -m 755 -o $(OWNER) -g $(GROUP) freeswitch/astpp-fs-xml.pl $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-fs-xml.cgi
	install -m 755 -o $(OWNER) -g $(GROUP) freeswitch/astpp-fs-cdr-xml.pl $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-fs-cdr-xml.cgi
	
	install -m 755 -o $(OWNER) -g $(GROUP) freeswitch/astpp-callingcards.pl ${FS_SCRIPTS}/astpp-callingcards.pl

#Freeswitch sounds installation function
install_freeswitch_sounds:
	for x in sounds/*.wav; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(FS_SOUNDSDIR); \
	done

#Freeswitch instruction print function
install_freeswitch_instructions:
	@echo "------------------------------";
	@echo "Sample Freeswitch configuration files live in";
	@echo "./freeswitch/conf.  You will need to modify your";
	@echo "files to be similar to those.";
	@echo "";
	@echo "Please run 'make install_astpp' to install web interface & configuration file.";
	@echo "ASTPP install appears to be successfull.";
	@echo "------------------------------";
	@echo "Please visit www.astpp.org for further instructions.";

#Asterisk config installation function	
install_asterisk_config:
	install -m 644 -o $(OWNER) -g $(GROUP) asterisk/confs/sample.astpp-dialplan.conf $(DESTDIR)$(ASTERISKDIR)/astpp-dialplan.conf

#Asterisk sounds installation function
install_asterisk_sounds:
	for x in sounds/*.wav; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(SOUNDSDIR); \
	done

#Asterisk AGI scripts installation function
install_asterisk_agi:
	for x in asterisk/agi/*.agi; do \
		echo $$x; \
		install -m 755 -o $(OWNER) -g $(GROUP) $$x $(PREFIX)$(AGIDIR); \
	done

#Asterisk instruction print function
install_asterisk_instructions:
	@echo "------------------------------";
	@echo "Please run 'make install_astpp' to install web interface & configuration file.";
	@echo "ASTPP install appears to be successfull.";
	@echo "------------------------------";
	@echo "Please visit www.astpp.org for further instructions.";
	
install_all_pre: install_astpp_folders install_astpp_scripts
install_freeswitch_conf: install_all_pre install_freeswitch_scripts install_freeswitch_sounds install_freeswitch_instructions
install_asterisk_conf: install_all_pre install_asterisk_config install_asterisk_sounds install_asterisk_agi install_asterisk_instructions
install_astpp: install_astpp_config install_astpp_gui

install:
	@echo "------------------------------";
	@echo "Please use 'make install_perl' to install perl packages";
	@echo "Please use 'make install_freeswitch_conf' if you are using Freeswitch";
	@echo "Please use 'make install_asterisk_conf' if you are using Asterisk";	
	@echo "Please use 'make install_astpp' to install web interface & configuration file.";
	@echo "------------------------------";
