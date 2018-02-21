/*
 * jQuery Galleriffic plugin
 *
 * Copyright (c) 2008 Trent Foley (http://trentacular.com)
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Thanks to Taku Sano (Mikage Sawatari), whose history plugin I adapted to work with Galleriffic
 * Modified by Ghismo (ghismo.com) to disable the location rewrite 
 */
;(function($) {

	// Write noscript style
	document.write("<style type='text/css'>.noscript{display:none}</style>");

	var ver = 'galleriffic-1.0';
	var galleryOffset = 0;
	var galleries = [];
	var allImages = [];	
	var historyCurrentHash;
	var historyBackStack;
	var historyForwardStack;
	var isFirst = false;
	var dontCheck = false;
	var isInitialized = false;

	function getHashFromString(hash) {
		if (!hash) return -1;
		hash = hash.replace(/^.*#/, '');
		if (isNaN(hash)) return -1;
		return (+hash);
	}

	function getHash() {
		var hash = location.hash;
		return getHashFromString(hash);
	}

	function registerGallery(gallery) {
		galleries.push(gallery);

		// update the global offset value
		galleryOffset += gallery.data.length;
	}

	function getGallery(hash) {
		for (i = 0; i < galleries.length; i++) {
			var gallery = galleries[i];
			if (hash < (gallery.data.length+gallery.offset))
				return gallery;
		}
		return 0;
	}
	
	function getIndex(gallery, hash) {
		return hash-gallery.offset;
	}
	
	function clickHandler(e, gallery, link) {
		gallery.pause();

		if (!gallery.settings.enableHistory) {
			var hash = getHashFromString(link.href);
			if (hash >= 0) {
				var index = getIndex(gallery, hash);
				if (index >= 0)
					gallery.goto(index);
			}
			e.preventDefault();
		}
	}

	function historyCallback() {
		// Using present location.hash always (seems to work, unlike the hash argument passed to this callback)
		var hash = getHash();
		if (hash < 0) return;

		var gallery = getGallery(hash);
		if (!gallery) return;
		
		var index = hash-gallery.offset;
		gallery.goto(index);
	}
	
	function historyInit() {
		if (isInitialized) return;
		isInitialized = true; 

		var current_hash = location.hash; //(enableHistory) ? location.hash : currentIndexHash; // Ghismo

		historyCurrentHash = current_hash;
		if ($.browser.msie) {
			// To stop the callback firing twice during initilization if no hash present
			if (historyCurrentHash == '') {
				historyCurrentHash = '#';
			}
		} else if ($.browser.safari) {
			// etablish back/forward stacks
			historyBackStack = [];
			historyBackStack.length = history.length;
			historyForwardStack = [];
			isFirst = true;
		}

		setInterval(function() { historyCheck(); }, 100);
	}
	
	function historyAddHistory(hash) {
		// This makes the looping function do something
		historyBackStack.push(hash);
		historyForwardStack.length = 0; // clear forwardStack (true click occured)
		isFirst = true;
	}
	
	function historyCheck() {
		if ($.browser.safari) {
			if (!dontCheck) {
				var historyDelta = history.length - historyBackStack.length;
				
				if (historyDelta) { // back or forward button has been pushed
					isFirst = false;
					if (historyDelta < 0) { // back button has been pushed
						// move items to forward stack
						for (var i = 0; i < Math.abs(historyDelta); i++) historyForwardStack.unshift(historyBackStack.pop());
					} else { // forward button has been pushed
						// move items to back stack
						for (var i = 0; i < historyDelta; i++) historyBackStack.push(historyForwardStack.shift());
					}
					var cachedHash = historyBackStack[historyBackStack.length - 1];
					if (cachedHash != undefined) {
						historyCurrentHash = location.hash; // (enableHistory) ? location.hash : currentIndexHash; // Ghismo
						historyCallback();
					}
				} else if (historyBackStack[historyBackStack.length - 1] == undefined && !isFirst) {
					historyCallback();
					isFirst = true;
				}
			}
		} else {
			// otherwise, check for location.hash
			var current_hash = location.hash; // (enableHistory) ? location.hash : currentIndexHash; // Ghismo
			if(current_hash != historyCurrentHash) {
				historyCurrentHash = current_hash;
				historyCallback();
			}
		}
	}

	var defaults = {
		delay:                  3000,
		numThumbs:              20,
		preloadAhead:           40, // Set to -1 to preload all images
		enableTopPager:         false,
		enableBottomPager:      true,
		imageContainerSel:      '',
		captionContainerSel:    '',
		controlsContainerSel:   '',
		loadingContainerSel:    '',
		renderSSControls:       true,
		renderNavControls:      true,
		playLinkText:           'Play',
		pauseLinkText:          'Pause',
		prevLinkText:           'Previous',
		nextLinkText:           'Next',
		nextPageLinkText:       'Next &rsaquo;',
		prevPageLinkText:       '&lsaquo; Prev',
		enableHistory:          false,
		autoStart:              false,
		onChange:               undefined, // accepts a delegate like such: function(prevIndex, nextIndex) { ... }
		onTransitionOut:        undefined, // accepts a delegate like such: function(callback) { ... }
		onTransitionIn:         undefined, // accepts a delegate like such: function() { ... }
		onPageTransitionOut:    undefined, // accepts a delegate like such: function(callback) { ... }
		onPageTransitionIn:     undefined  // accepts a delegate like such: function() { ... }
	};

	$.fn.galleriffic = function(thumbsContainerSel, settings) {
		//  Extend Gallery Object
		$.extend(this, {
			ver: function() {
				return ver;
			},

			initializeThumbs: function() {
				this.data = [];
				var gallery = this;
				
				this.$thumbsContainer.find('ul.thumbs > li').each(function(i) {
					var $li = $(this);
					var $aThumb = $li.find('a.thumb');
					var hash = gallery.offset+i;

					gallery.data.push({
						title:$aThumb.attr('title'),
						slideUrl:$aThumb.attr('href'),
						caption:$li.find('.caption').remove(),
						hash:hash
					});

					// Setup history
					$aThumb.attr('rel', 'history');
					$aThumb.attr('href', '#'+hash);
					$aThumb.click(function(e) {
						clickHandler(e, gallery, this);
					});
				});
				return this;
			},

			isPreloadComplete: false,

			preloadInit: function() {
				if (this.settings.preloadAhead == 0) return this;
				
				this.preloadStartIndex = this.currentIndex;
				var nextIndex = this.getNextIndex(this.preloadStartIndex);
				return this.preloadRecursive(this.preloadStartIndex, nextIndex);
			},
			
			preloadRelocate: function(index) {
				// By changing this startIndex, the current preload script will restart
				this.preloadStartIndex = index;
				return this;
			},

			preloadRecursive: function(startIndex, currentIndex) {
				// Check if startIndex has been relocated
				if (startIndex != this.preloadStartIndex) {
					var nextIndex = this.getNextIndex(this.preloadStartIndex);
					return this.preloadRecursive(this.preloadStartIndex, nextIndex);
				}

				var gallery = this;

				// Now check for preloadAhead count
				var preloadCount = currentIndex - startIndex;
				if (preloadCount < 0)
					preloadCount = this.data.length-1-startIndex+currentIndex;
				if (this.settings.preloadAhead >= 0 && preloadCount > this.settings.preloadAhead) {
					// Do this in order to keep checking for relocated start index
					setTimeout(function() { gallery.preloadRecursive(startIndex, currentIndex); }, 500);
					return this;
				}

				var imageData = this.data[currentIndex];
				if (!imageData)
					return this;

				// If already loaded, continue
				if (imageData.image)
					return this.preloadNext(startIndex, currentIndex); 
				
				// Preload the image
				var image = new Image();
				
				image.onload = function() {
					imageData.image = this;
					gallery.preloadNext(startIndex, currentIndex);
				};

				image.alt = imageData.title;
				image.src = imageData.slideUrl;

				return this;
			},
			
			preloadNext: function(startIndex, currentIndex) {
				var nextIndex = this.getNextIndex(currentIndex);
				if (nextIndex == startIndex) {
					this.isPreloadComplete = true;
				} else {
					// Use set timeout to free up thread
					var gallery = this;
					setTimeout(function() { gallery.preloadRecursive(startIndex, nextIndex); }, 100);
				}
				return this;
			},

			getNextIndex: function(index) {
				var nextIndex = index+1;
				if (nextIndex >= this.data.length)
					nextIndex = 0;
				return nextIndex;
			},
			
			getPrevIndex: function(index) {
				var prevIndex = index-1;
				if (prevIndex < 0)
					prevIndex = this.data.length-1;
				return prevIndex;
			},

			pause: function() {
				if (this.interval)
					this.toggleSlideshow();
				
				return this;
			},

			play: function() {
				if (!this.interval)
					this.toggleSlideshow();
				
				return this;
			},

			toggleSlideshow: function() {
				if (this.interval) {
					clearInterval(this.interval);
					this.interval = 0;
					
					if (this.$controlsContainer) {
						this.$controlsContainer
							.find('div.ss-controls a').removeClass().addClass('play')
							.attr('title', this.settings.playLinkText)
							.attr('href', '#play')
							.html(this.settings.playLinkText);
					}
				} else {
					this.ssAdvance();

					var gallery = this;
					this.interval = setInterval(function() {
						gallery.ssAdvance();
					}, this.settings.delay);
					
					if (this.$controlsContainer) {
						this.$controlsContainer
							.find('div.ss-controls a').removeClass().addClass('pause')
							.attr('title', this.settings.pauseLinkText)
							.attr('href', '#pause')
							.html(this.settings.pauseLinkText);
					}
				}

				return this;
			},

			ssAdvance: function() {
				var nextIndex = this.getNextIndex(this.currentIndex);
				var nextHash = this.data[nextIndex].hash;

				// Seems to be working on both FF and Safari
				if (this.settings.enableHistory)
					location.href = '#'+nextHash;
				else
					this.goto(nextIndex);

				// IE we need to explicity call goto
				//if ($.browser.msie) {
				//	this.goto(nextIndex);
				//}

				return this;
			},

			goto: function(index) {
				if (index < 0) index = 0;
				else if (index >= this.data.length) index = this.data.length-1;
				
				if (this.settings.onChange)
					this.settings.onChange(this.currentIndex, index);
				
				this.currentIndex = index;
				this.preloadRelocate(index);
				return this.refresh();
			},
			
			refresh: function() {
				var imageData = this.data[this.currentIndex];
				if (!imageData)
					return this;
				
				// Flag we are transitioning
				var isTransitioning = true;

				var gallery = this;

				var transitionOutCallback = function() {
					// Flag that the transition has completed
					isTransitioning = false;

					// Update Controls
					if (gallery.$controlsContainer) {
						gallery.$controlsContainer
							.find('div.nav-controls a.prev').attr('href', '#'+gallery.data[gallery.getPrevIndex(gallery.currentIndex)].hash).end()
							.find('div.nav-controls a.next').attr('href', '#'+gallery.data[gallery.getNextIndex(gallery.currentIndex)].hash);
					}

					var imageData = gallery.data[gallery.currentIndex];

					// Replace Caption
					if (gallery.$captionContainer) {
						gallery.$captionContainer.empty().append(imageData.caption);
					}

					if (imageData.image) {
						gallery.buildImage(imageData.image);
					} else {
						// Show loading container
						if (gallery.$loadingContainer) {
							gallery.$loadingContainer.show();
						}
					}
				}

				if (this.settings.onTransitionOut) {
					this.settings.onTransitionOut(transitionOutCallback);
				} else {
					this.$transitionContainers.hide();
					transitionOutCallback();
				}

				if (!imageData.image) {
					var image = new Image();
					
					// Wire up mainImage onload event
					image.onload = function() {
						imageData.image = this;

						if (!isTransitioning) {
							gallery.buildImage(imageData.image);
						}
					};

					// set alt and src
					image.alt = imageData.title;
					image.src = imageData.slideUrl;
				}

				// This causes the preloader (if still running) to relocate out from the currentIndex
				this.relocatePreload = true;

				return this.syncThumbs();
			},
			
			buildImage: function(image) {
				if (this.$imageContainer) {
					this.$imageContainer.empty();

					var gallery = this;
					var nextIndex = this.getNextIndex(this.currentIndex);

					// Hide the loading conatiner
					if (this.$loadingContainer) {
						this.$loadingContainer.hide();
					}

					// Setup image
					this.$imageContainer
						.append('<span class="image-wrapper"><a class="advance-link" rel="history" href="#'+this.data[nextIndex].hash+'" title="'+image.alt+'"></a></span>')
						.find('a')
						.append(image)
						.click(function(e) {
							clickHandler(e, gallery, this);
						});
				}

				if (this.settings.onTransitionIn)
					this.settings.onTransitionIn();
				else
					this.$transitionContainers.show();

				return this;
			},

			syncThumbs: function() {
				if (this.$thumbsContainer) {
					var page = Math.floor(this.currentIndex / this.settings.numThumbs);
					if (page != this.currentPage) {
						this.currentPage = page;
						this.updateThumbs();
					}

					// Remove existing selected class and add selected class to new thumb
					var $thumbs = this.$thumbsContainer.find('ul.thumbs').children();
					$thumbs.filter('.selected').removeClass('selected');
					$thumbs.eq(this.currentIndex).addClass('selected');
				}

				return this;
			},

			updateThumbs: function() {
				var gallery = this;
				var transitionOutCallback = function() {
					gallery.rebuildThumbs();

					// Transition In the thumbsContainer
					if (gallery.settings.onPageTransitionIn)
						gallery.settings.onPageTransitionIn();
					else
						gallery.$thumbsContainer.show();
				};

				// Transition Out the thumbsContainer
				if (this.settings.onPageTransitionOut) {
					this.settings.onPageTransitionOut(transitionOutCallback);
				} else {
					this.$thumbsContainer.hide();
					transitionOutCallback();
				}

				return this;
			},

			rebuildThumbs: function() {
				// Initialize currentPage to first page
				if (this.currentPage < 0)
					this.currentPage = 0;
				
				var needsPagination = this.data.length > this.settings.numThumbs;

				// Rebuild top pager
				var $topPager = this.$thumbsContainer.find('div.top');
				if ($topPager.length == 0)
					$topPager = this.$thumbsContainer.prepend('<div class="top pagination"></div>').find('div.top');

				if (needsPagination && this.settings.enableTopPager) {
					$topPager.empty();
					this.buildPager($topPager);
				}

				// Rebuild bottom pager
				if (needsPagination && this.settings.enableBottomPager) {
					var $bottomPager = this.$thumbsContainer.find('div.bottom');
					if ($bottomPager.length == 0)
						$bottomPager = this.$thumbsContainer.append('<div class="bottom pagination"></div>').find('div.bottom');
					else
						$bottomPager.empty();

					this.buildPager($bottomPager);
				}

				var startIndex = this.currentPage*this.settings.numThumbs;
				var stopIndex = startIndex+this.settings.numThumbs-1;
				if (stopIndex >= this.data.length)
					stopIndex = this.data.length-1;

				// Show/Hide thumbs
				var $thumbsUl = this.$thumbsContainer.find('ul.thumbs');
				$thumbsUl.find('li').each(function(i) {
					var $li = $(this);
					if (i >= startIndex && i <= stopIndex) {
						$li.show();
					} else {
						$li.hide();
					}
				});

				// Remove the noscript class from the thumbs container ul
				$thumbsUl.removeClass('noscript');
				
				return this;
			},

			buildPager: function(pager) {
				var gallery = this;
				var startIndex = this.currentPage*this.settings.numThumbs;
				
				// Prev Page Link
				if (this.currentPage > 0) {
					var prevPage = startIndex - this.settings.numThumbs;
					pager.append('<a rel="history" href="#'+this.data[prevPage].hash+'" title="'+this.settings.prevPageLinkText+'">'+this.settings.prevPageLinkText+'</a>');
				}

				// Page Index Links
				for (i=this.currentPage-3; i<=this.currentPage+3; i++) {
					var pageNum = i+1;
					
					if (i == this.currentPage)
						pager.append('<span class="current">'+pageNum+'</span>');
					else if (i>=0 && i<this.numPages) {
						var imageIndex = i*this.settings.numThumbs;
						pager.append('<a rel="history" href="#'+this.data[imageIndex].hash+'" title="'+pageNum+'">'+pageNum+'</a>');
					}
				}

				// Next Page Link
				var nextPage = startIndex+this.settings.numThumbs;
				if (nextPage < this.data.length) {
					pager.append('<a rel="history" href="#'+this.data[nextPage].hash+'" title="'+this.settings.nextPageLinkText+'">'+this.settings.nextPageLinkText+'</a>');
				}

				pager.find('a').click(function(e) {
					clickHandler(e, gallery, this);
				});

				return this;
			}
		});

		// Now initialize the gallery
		this.settings = $.extend({}, defaults, settings);
		//enableHistory = this.settings.enableHistory; // Ghismo

		if (this.interval)
			clearInterval(this.interval);

		this.interval = 0;
		
		if (this.settings.imageContainerSel) this.$imageContainer = $(this.settings.imageContainerSel);
		if (this.settings.captionContainerSel) this.$captionContainer = $(this.settings.captionContainerSel);
		if (this.settings.loadingContainerSel) this.$loadingContainer = $(this.settings.loadingContainerSel);

		// Setup the jQuery object holding each container that will be transitioned
		this.$transitionContainers = $([]);
		if (this.$imageContainer)
			this.$transitionContainers = this.$transitionContainers.add(this.$imageContainer);
		if (this.$captionContainer)
			this.$transitionContainers = this.$transitionContainers.add(this.$captionContainer);
		
		// Set the hash index offset for this gallery
		this.offset = galleryOffset;

		this.$thumbsContainer = $(thumbsContainerSel);
		this.initializeThumbs();

		// Add this gallery to the global galleries array
		registerGallery(this);

		this.numPages = Math.ceil(this.data.length/this.settings.numThumbs);
		this.currentPage = -1;
		this.currentIndex = 0;
		var gallery = this;

		// Hide the loadingContainer
		if (this.$loadingContainer)
			this.$loadingContainer.hide();

		// Setup controls
		if (this.settings.controlsContainerSel) {
			this.$controlsContainer = $(this.settings.controlsContainerSel).empty();
			
			if (this.settings.renderSSControls) {
				if (this.settings.autoStart) {
					this.$controlsContainer
						.append('<div class="ss-controls"><a href="#pause" class="pause" title="'+this.settings.pauseLinkText+'">'+this.settings.pauseLinkText+'</a></div>');
				} else {
					this.$controlsContainer
						.append('<div class="ss-controls"><a href="#play" class="play" title="'+this.settings.playLinkText+'">'+this.settings.playLinkText+'</a></div>');
				}

				this.$controlsContainer.find('div.ss-controls a')
					.click(function(e) {
						gallery.toggleSlideshow();
						e.preventDefault();
						return false;
					});
			}
		
			if (this.settings.renderNavControls) {
				var $navControls = this.$controlsContainer
					.append('<div class="nav-controls"><a class="prev" rel="history" title="'+this.settings.prevLinkText+'">'+this.settings.prevLinkText+'</a><a class="next" rel="history" title="'+this.settings.nextLinkText+'">'+this.settings.nextLinkText+'</a></div>')
					.find('div.nav-controls a')
					.click(function(e) {
						clickHandler(e, gallery, this);
					});
			}
		}

		// Initialize history only once when the first gallery on the page is initialized
		historyInit();
		
		// Build image
		var hash = getHash();
		var hashGallery = (hash >= 0) ? getGallery(hash) : 0;
		var gotoIndex = (hashGallery && this == hashGallery) ? (hash-this.offset) : 0;
		this.goto(gotoIndex);

		if (this.settings.autoStart) {
			
			setTimeout(function() { gallery.play(); }, this.settings.delay);
		}

		// Kickoff Image Preloader after 1 second
		setTimeout(function() { gallery.preloadInit(); }, 1000);

		return this;
	};
})(jQuery);