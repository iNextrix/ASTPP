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
	
resolve_dep:
	
	yum -y install autoconf automake bzip2 cpio curl curl-devel curl-devel expat-devel fileutils gcc-c++ gettext-devel gnutls-devel libjpeg-devel libogg-devel libtiff-devel libtool libvorbis-devel make ncurses-devel nmap openssl openssl-devel openssl-devel perl patch unixODBC unixODBC-devel unzip wget zip zlib zlib-devel git libxml2 libxml2-devel mysql mysql-server mysql-devel
	
	cpan -i Bundle::CPAN;
	cpan -i ExtUtils::CBuilder;
	cpan -i Text::Template;
	cpan -i Params::Check
	cpan -i install Locale::gettext_pp;
	cpan -i install Locale::Country;
	cpan -i install Locale::Language;
	cpan -i install DBI;
	cpan -i install DBD::mysql;
	cpan -i YAML;
	cpan -i install Params::Validate;
	cpan -i install CGI;
	cpan -i install Asterisk::AGI;
	cpan -i install LWP::Simple;
	cpan -i install URI::Escape;
	cpan -i install POE::Component::Client::Asterisk::Manager;
	cpan -i install Getopt::Long;
	cpan -i install Text::CSV;
	cpan -i install Mail::Sendmail;	
	cpan -i install Email::Simple;	
	cpan -i install Time::DaysInMonth;	
	cpan -i install Data::Paginate;
	cpan -i install HTML::Template;
	cpan -i install HTML::Template::Expr;
	cpan -i install DateTime;
	cpan -i install DateTime::TimeZone;
	cpan -i install DateTime::Locale;		
	cpan -i install XML::Simple;
	cpan -i install XML::LibXML;	
	cpan -i install Module::Build;
	cpan -i install Class::Singleton;
	cpan -i install Data::Dumper;
	cpan -i install Module::Build;
	cpan -i install Class::Singleton;
	cpan -i install Text::Template;
	cpan -i install Locale::Country;
	cpan -i install Data::Dumper;
	cpan -i install IO::Tty;	
	cpan -i install IO::All;     
	cpan -i install Test::Pod;
	cpan -i install MIME::Types;
	cpan -i install POE::Test::Loops;
	cpan -i install Storable;
	cpan -i install Time::Zone;
	cpan -i install Date::Parse;
	cpan -i install Curses;
	cpan -i install IO::String;
	cpan -i install POE;
	cpan -i install Sys::Syslog;
	cpan -i install Log::Dispatch;
	cpan -i install Test::Simple;
	cpan -i install FCGI;
	cpan -i install Email::Date::Format;
	cpan -i install ExtUtils::CBuilder;
	cpan -i install Set::Infinite;
	cpan -i install DateTime::Set;
	cpan -i install DateTime::Event::Recurrence;
	cpan -i install DateTime::Incomplete;
	cpan -i install Class::Data::Inheritable;
	cpan -i install Locale::Country::Multilingual;
	cpan -i install File::Slurp;
	cpan -i install Template::Simple;
	cpan -i install Template::Plugin::DateTime;
	cpan -i install Date::Language;
	cpan -i install Date::Format;
	cpan -i install Text::CSV_XS;
	cpan -i install Term::ReadKey;
	cpan -i install DateTime::Format::Strptime;
	cpan -i install DBI::Shell;
	cpan -i install Net::Daemon;
	cpan -i install DBD::Mutiplex;
	cpan -i install TimeDate Mail::Tools;
	cpan -i install Asterisk::Manager;
	cpan -i install Asterisk::Outgoing;
	cpan -i install Asterisk::Voicemail;
	cpan -i install Template::Toolkit;
	cpan -i install Date::Language;
	cpan -i install Date::Format;
	cpan -i install DateTime::Locale;
	cpan -i install XML::Writer
	cd modules/ASTPP && perl Makefile.PL && make && make install && cd ../../
	
install_instructions_print:
	@echo "------------------------------";
	@echo "Please run 'make install_astpp' to install templates and astpp configuration file.";
	@echo "ASTPP install appears to be successfull.";
	@echo "------------------------------";
	@echo "Please visit www.astpp.org for further instructions.";


install_instructions_print_freeswitch:
	@echo "------------------------------";
	@echo "Sample Freeswitch configuration files live in";
	@echo "./freeswitch/conf.  You will need to modify your";
	@echo "files to be similar to those.";
	@echo "";
	@echo "Please run 'make install_astpp' to install templates and astpp configuration file.";
	@echo "ASTPP install appears to be successfull.";
	@echo "------------------------------";
	@echo "Please visit www.astpp.org for further instructions.";	
	
install_all_pre: install_misc install_astpp_exec
install_all_post: install_instructions_print

install_asterisk_conf: install_all_pre install_asterisk_config install_sounds_asterisk install_agi install_all_post
install_freeswitch_conf: install_all_pre install_sounds_freeswitch install_instructions_print_freeswitch

install_astpp: install_images install_templates install_samples

install:
	@echo "------------------------------";
	@echo "Please use 'make resolve_dep' to confirm perl packages";
	@echo "Please use 'make install_asterisk_conf' if you are using Asterisk";	
	@echo "Please use 'make install_freeswitch_conf' you are using Freeswitch";
	@echo "Please use 'make install_astpp' to install templates and astpp configuration file.";
	@echo "------------------------------";