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
ASTPPEXECDIR=/usr/local/astpp/
ASTPPLOGDIR=/var/log/astpp/
EXTENSIONS=/etc/asterisk/extensions.conf
LOCALE_DIR=/usr/local/share/locale
OWNER=root
GROUP=root
WWWDIR=/var/www

all:

install_misc:
		mkdir -p $(DESTDIR)$(ASTPPDIR)
		mkdir -p $(DESTDIR)$(ASTPPLOGDIR)
		mkdir -p $(DESTDIR)$(ASTPPEXECDIR)
		mkdir -p $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin
		mkdir -p $(DESTDIR)$(WWWDIR)/cgi-bin/astpp
		mkdir -p $(DESTDIR)$(WWWDIR)/html/_astpp
		chown $(OWNER) $(DESTDIR)$(ASTPPDIR)
		chown $(OWNER) $(DESTDIR)$(ASTPPLOGDIR)
		chown $(OWNER) $(DESTDIR)$(ASTPPEXECDIR)
		chgrp $(GROUP) $(DESTDIR)$(ASTPPDIR)
		chgrp $(GROUP) $(DESTDIR)$(ASTPPLOGDIR)
		chgrp $(GROUP) $(DESTDIR)$(ASTPPEXECDIR)
		install -m 755 -o $(OWNER) -g $(GROUP) astpp-callback.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-callback.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) astpp-pricelist.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-pricelist.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) astpp-refill.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-refill.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) astpp-admin.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin/astpp-admin.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) astpp-auto-admin.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp-admin/astpp-auto-admin.cgi
		install -m 755 -o $(OWNER) -g $(GROUP) astpp-users.cgi $(DESTDIR)$(WWWDIR)/cgi-bin/astpp/astpp-users.cgi
		install -m 644 -o $(OWNER) -g $(GROUP) style.css $(DESTDIR)$(WWWDIR)/html/_astpp/
		install -m 644 -o $(OWNER) -g $(GROUP) menu.js $(DESTDIR)$(WWWDIR)/html/_astpp/
		# Install Sample Configuration Files
		install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.reseller-config.conf $(DESTDIR)$(ASTPPDIR)/sample.reseller-config.conf
		install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-config.conf $(DESTDIR)$(ASTPPDIR)/sample.astpp-config.conf
		install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-enh-config.conf $(DESTDIR)$(ASTPPDIR)/sample.astpp-enh-config.conf
		install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-dialplan.conf $(DESTDIR)$(ASTERISKDIR)/sample.astpp-dialplan.conf

install_samples:
	install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.reseller-config.conf $(DESTDIR)$(ASTPPDIR)/sample.reseller-config.conf
	install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-config.conf $(DESTDIR)$(ASTPPDIR)/astpp-config.conf
	install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-enh-config.conf $(DESTDIR)$(ASTPPDIR)/astpp-enh-config.conf
	install -m 644 -o $(OWNER) -g $(GROUP) ./samples/sample.astpp-dialplan.conf $(DESTDIR)$(ASTERISKDIR)/astpp-dialplan.conf

install_php:
	mkdir -p $(DESTDIR)$(WWWDIR)/html/astpp
	chgrp $(GROUP) $(DESTDIR)$(WWWDIR)/html/astpp
	install -m 644 -o $(OWNER) -g $(GROUP) -d ./php-interface/frames $(DESTDIR)$(WWWDIR)/html/astpp/

		
install_agi:
	for x in *.agi; do \
		echo $$x; \
		install -m 755 -o $(OWNER) -g $(GROUP) $$x $(PREFIX)$(AGIDIR); \
	done

install_astpp_exec:
	for x in ./*.pl; do \
		echo $$x; \
		install -m 755 -o $(OWNER) -g $(GROUP) $$x $(PREFIX)$(ASTPPEXECDIR); \
	done

install_sounds:
	for x in sounds/*.gsm; do \
		echo $$x;\
		install -m 644 $$x $(DESTDIR)$(SOUNDSDIR); \
	done
	
install_images:
	for x in images/*.jpg; do \
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
	perl -MCPAN -e "install CGI";
	perl -MCPAN -e "install Asterisk::AGI";
	perl -MCPAN -e "install LWP::Simple";
	perl -MCPAN -e "install URI::Escape";
	perl -MCPAN -e "install POE::Component::Client::Asterisk::Manager";
	perl -MCPAN -e "install Getopt::Long";
	perl -MCPAN -e "install Text::CSV";
	perl -MCPAN -e "install Mail::Sendmail";	
	perl -MCPAN -e "install Email::Simple";	
#	perl -MCPAN -e "install POSIX";
#	perl -MCPAN -e "install Data::Dumper";

install_instructions_print:
	@echo "------------------------------";
	@echo "ASTPP install appears to be successfull.";
	@echo "------------------------------";
	@echo "It is now time to prepare the database and the inital configuration files.";
	@echo "To create the ASTPP database use the following commands as root on your system:";
	@echo "mysqladmin create astpp";
	@echo "This will have created a database called astpp.  It's now time to populate that database with the necessary tables.";
	@echo "mysql -u root -p astpp < sql/astpp-1.4.sql";
	@echo "You will be prompted for the password.";
	@echo "If you do not currently have a cdr database, please create one by following these commands:";
	@echo "mysqladmin create asteriskcdrdb";
	@echo "mysql -u root -p astpp < sql/asteriskcdrdb.sql";
	@echo "You will be prompted for the password.";
	@echo "Once these databases have been created please be sure to edit /var/lib/astpp/astpp-config.conf";
	@echo "to update your database connection information.  It is also necessary to edit /var/lib/astpp/astpp-enh-config.conf";
	@echo "to change the 'auth' code as well as set more advanced features."
	@echo "";
	@echo "'make install' updates all the sample files which have been installed but to overwrite any settings you have in place";
	@echo "perform a 'make install_samples'.  Be aware that this will overwrite any astpp settings you may have as well as replace";
	@echo "your astpp-dialplan.conf file.";
	@echo "";
	@echo "Thank you for using ASTPP!  Please visit www.astpp.org for support information.";

install: all install_misc install_images install_astpp_exec install_agi install_sounds install_instructions_print

