#Makefile for ASTPP (www.astpp.org)
SHELL=/bin/sh
ASTERISKDIR=/etc/asterisk
IAX=/etc/asterisk/iax.conf
EXTENSIONS_AMP=/etc/asterisk/extensions_additional.conf
SIP_AMP=/etc/asterisk/sip_additional.conf
IAX_AMP=/etc/asterisk/iax_additional.conf
PERL_LIST=one two three
AGIDIR=/var/lib/asterisk/agi-bin
SOUNDSDIR=/var/lib/asterisk/sounds
ASTPPDIR=/var/lib/astpp/
TEMPLATEDIR=/var/lib/astpp/templates/
ASTPPEXECDIR=/usr/local/astpp/
ASTPPLOGDIR=/var/log/astpp/
EXTENSIONS=/etc/asterisk/extensions.conf
LOCALE_DIR=/usr/local/share/locale

FS_SOUNDSDIR=/usr/local/freeswitch/sounds/en/us/callie
FS_SCRIPTS=/usr/local/freeswitch/scripts

OWNER=root
GROUP=root
WWWDIR=/var/www

all: install

install_misc:
		mkdir -p $(DESTDIR)$(ASTPPDIR)
		mkdir -p $(DESTDIR)$(ASTPPLOGDIR)
		mkdir -p $(DESTDIR)$(ASTPPEXECDIR)
		mkdir -p $(DESTDIR)$(TEMPLATEDIR)
		mkdir -p $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin
		mkdir -p $(DESTDIR)$(WWWDIR)/cgi-bin/astpp
		mkdir -p $(DESTDIR)$(WWWDIR)/html/_astpp
		chown $(OWNER) $(DESTDIR)$(ASTPPDIR)
		chown $(OWNER) $(DESTDIR)$(ASTPPLOGDIR)
		chown $(OWNER) $(DESTDIR)$(ASTPPEXECDIR)
		chgrp $(GROUP) $(DESTDIR)$(ASTPPDIR)
		chgrp $(GROUP) $(DESTDIR)$(ASTPPLOGDIR)
		chgrp $(GROUP) $(DESTDIR)$(ASTPPEXECDIR)
		# Install Freeswitch .pl files as .cgi files
		install -m 755 -o $(OWNER) -g $(GROUP) freeswitch/astpp-fs-xml.pl $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-fs-xml.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) freeswitch/astpp-fs-cdr-xml.pl $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-fs-cdr-xml.cgi
		#
		install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-callback.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-callback.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-pricelist.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-pricelist.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-refill.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-refill.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-admin.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin/astpp-admin.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-auto-admin.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin/astpp-auto-admin.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) web_interface/astpp-users.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp/astpp-users.cgi
		install -m 644 -o $(OWNER) -g $(GROUP) web_interface/style.css $(DESTDIR)$(WWWDIR)/html/_astpp/
		install -m 644 -o $(OWNER) -g $(GROUP) web_interface/menu.js $(DESTDIR)$(WWWDIR)/html/_astpp/

		# Install Sample Configuration Files
		install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-config.conf $(DESTDIR)$(ASTPPDIR)/sample.astpp-config.conf

install_asterisk_config:
		install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-dialplan.conf $(DESTDIR)$(ASTERISKDIR)/sample.astpp-dialplan.conf

install_freeswitch_config:
	@echo "--------------------";
	@echo "Sample Freeswitch configuration files live in";
	@echo "./freeswitch/conf.  You will need to modify your";
	@echo "files to be similar to those.";
	@echo "--------------------";

install_samples:
	install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-config.conf $(DESTDIR)$(ASTPPDIR)/astpp-config.conf

install_php:
	mkdir -p $(DESTDIR)$(WWWDIR)/html/astpp
	chgrp $(GROUP) $(DESTDIR)$(WWWDIR)/html/astpp
	install -m 644 -o $(OWNER) -g $(GROUP) -d ./php-interface/frames $(DESTDIR)$(WWWDIR)/html/astpp/

		
install_agi:
	for x in asterisk_apps/*.agi; do \
		echo $$x; \
		install -m 755 -o $(OWNER) -g $(GROUP) $$x $(PREFIX)$(AGIDIR); \
	done

install_astpp_exec:
	for x in scripts/*.pl; do \
		echo $$x; \
		install -m 755 -o $(OWNER) -g $(GROUP) $$x $(PREFIX)$(ASTPPEXECDIR); \
	done

install_sounds_asterisk:
	for x in sounds/*.gsm; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(SOUNDSDIR); \
	done

install_sounds_freeswitch:
	for x in sounds/GSM/*.GSM; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(FS_SOUNDSDIR); \
	done
	for x in sounds/*.gsm; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(FS_SOUNDSDIR); \
	done
	for x in sounds/WAV/*.WAV; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(FS_SOUNDSDIR); \
	done


install_templates:
	for x in templates/*; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(TEMPLATEDIR); \
	done
	
install_images:
	for x in images/*.jpg; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(WWWDIR)/html/_astpp/; \
	done
		install -m 644 images/favicon.ico $(DESTDIR)$(WWWDIR)/html/_astpp/;
	for x in images/*.png; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(WWWDIR)/html/_astpp/; \
	done
	
resolve_perl_dep:
	perl -MCPAN -e "install Text::Template";		
	perl -MCPAN -e "install Locale::gettext_pp";
	perl -MCPAN -e "install Locale::Country";
	perl -MCPAN -e "install Locale::Language";
	perl -MCPAN -e "install DBI";
	perl -MCPAN -e "install DBD::mysql";
#	perl -MCPAN -e "install DBD::Pg";
	perl -MCPAN -e "install Params::Validate";
	perl -MCPAN -e "install CGI";
	perl -MCPAN -e "install Asterisk::AGI";
	perl -MCPAN -e "install LWP::Simple";
	perl -MCPAN -e "install URI::Escape";
	perl -MCPAN -e "install POE::Component::Client::Asterisk::Manager";
	perl -MCPAN -e "install Getopt::Long";
	perl -MCPAN -e "install Text::CSV";
	perl -MCPAN -e "install Mail::Sendmail";	
	perl -MCPAN -e "install Email::Simple";	
	perl -MCPAN -e "install Time::DaysInMonth";	
	perl -MCPAN -e "install Data::Paginate";
	perl -MCPAN -e "install HTML::Template";
	perl -MCPAN -e "install HTML::Template::Expr";
	perl -MCPAN -e "install DateTime";
	perl -MCPAN -e "install DateTime::TimeZone";
	perl -MCPAN -e "install DateTime::Locale";
	perl -MCPAN -e "install DateTime";
	perl -MCPAN -e "install Locale::gettext_pp";
	perl -MCPAN -e "install XML::Simple";
	perl -MCPAN -e "install XML::LibXML";
	cd modules/ASTPP && perl Makefile.PL && make && make install && cd ../../

	

install_instructions_print:
	@echo "------------------------------";
	@echo "ASTPP install appears to be successfull.";
	@echo "------------------------------";
	@echo "Please visit www.astpp.org for further instructions.";

install_all_pre: install_misc install_astpp_exec
install_all_post: install_instructions_print

install_asterisk: install_all_pre install_asterisk_config install_sounds_asterisk install_agi install_all_post
install_freeswitch: install_all_pre install_freeswitch_config install_sounds_freeswitch install_all_post

samples: install_images install_templates install_samples

install:
	@echo "------------------------------";
	@echo "Please use 'make install_asterisk if you are using Asterisk";
	@echo "Please use 'make install_freeswitch you are using Freeswitch";
	@echo "------------------------------";

