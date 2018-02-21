# Makefile For aleph-aac.
# For help visit www.astpp.org.
#

OWNER=	root
GROUP=	wheel
AGIDIR= /var/lib/asterisk/agi-bin
BINDIR= /usr/local/bin
AACDIR= /var/lib/aac


install:
	mkdir -p $(DESTDIR)$(AACDIR)
	install -m 755 -o $(OWNER) -g $(GROUP) aac.conf $(DESTDIR)$(AACDIR)
	install -m 755 -o $(OWNER) -g $(GROUP) aleph-aac.agi $(DESTDIR)$(AGIDIR)
	install -m 755 -o $(OWNER) -g $(GROUP) aleph-autocall.pl $(DESTDIR)$(BINDIR)
	install -m 755 -o $(OWNER) -g $(GROUP) aleph-autocall-monitor.pl $(DESTDIR)$(BINDIR)
	echo "Aleph Auto Call has now been installed!"
