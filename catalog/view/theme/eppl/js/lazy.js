"use strict";

// Lazy loading img & background images using intersection observer
// Reference: https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/
// Using example: <img class="lazy" src="thumb.gif" data-src="real-img.jpg" data-srcset="real-img@1x.jpg 1x, real-img@2x.jpg 2x">
// Background image class usign example: <div class="lazy-background"> with added class ".visible" for styling
// Background image style attribute lazy loading example: <div data-bg="image.jpg">
// delete window.IntersectionObserver; // Fallback Testing
// let btnToggleMenuMobile = [].slice.call(document.querySelectorAll(".toggle-menuMobile-mobile--js"));
// let menuMobile = document.querySelector(".menuMobile-mobile--js");
// let menuMobileLink = [].slice.call(document.querySelectorAll(".menuMobile-mobile--js ul li a"));
document.addEventListener('DOMContentLoaded', function () {
	var rootMargin = "500px 0px 500px 0px";
	var lazyImages = [].slice.call(document.querySelectorAll("picture.lazy img, picture.lazy source, img.lazy"));
	var lazyBackgrounds = [].slice.call(document.querySelectorAll('.lazy-background'));
	var lazyBackgroundsData = [].slice.call(document.querySelectorAll('[data-bg]'));

	if ('IntersectionObserver' in window) {
		var lazyImageObserver = new IntersectionObserver(function (entries, observer) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					var lazyImage = entry.target;

					if (lazyImage.tagName == 'IMG') {
						lazyImage.src = lazyImage.dataset.src;
						lazyImage.removeAttribute("data-src");

						if (lazyImage.dataset.srcset) {
							lazyImage.srcset = lazyImage.dataset.srcset;
							lazyImage.removeAttribute("data-srcset");
						}
					}

					if (lazyImage.tagName == 'SOURCE') {
						lazyImage.srcset = lazyImage.dataset.srcset;
						lazyImage.removeAttribute("data-srcset");
					} // lazyImage.src = lazyImage.dataset.src;


					lazyImage.classList.remove('lazy');
					lazyImageObserver.unobserve(lazyImage);
				}
			});
		}, {
			rootMargin: rootMargin
		});
		lazyImages.forEach(function (lazyImage) {
			lazyImageObserver.observe(lazyImage);
		});
		var lazyBackgroundObserver = new IntersectionObserver(function (entries, observer) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('visible');
					lazyBackgroundObserver.unobserve(entry.target);
				}
			});
		}, {
			rootMargin: rootMargin
		});
		lazyBackgrounds.forEach(function (lazyBackground) {
			lazyBackgroundObserver.observe(lazyBackground);
		});
		var lazyBackgroundDataObserver = new IntersectionObserver(function (entries, observer) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					var lazyBackgroundData = entry.target;
					lazyBackgroundData.style.backgroundImage = 'url(' + lazyBackgroundData.dataset.bg + ')';
					lazyBackgroundDataObserver.unobserve(lazyBackgroundData);
				}
			});
		}, {
			rootMargin: rootMargin
		});
		lazyBackgroundsData.forEach(function (lazyBackgroundData) {
			lazyBackgroundDataObserver.observe(lazyBackgroundData);
		});
	} else {
		// Fallback
		lazyImages.forEach(function (lazyImage) {
			// lazyImage.src = lazyImage.dataset.src;
			// lazyImage.srcset = lazyImage.dataset.srcset;
			if (lazyImage.tagName == 'IMG') {
				lazyImage.src = lazyImage.dataset.src;
			}

			if (lazyImage.tagName == 'SOURCE') {
				lazyImage.srcset = lazyImage.dataset.srcset;
			}
		});
		lazyBackgrounds.forEach(function (lazyBackground) {
			lazyBackground.classList.add('visible');
		});
		lazyBackgroundsData.forEach(function (lazyBackgroundData) {
			lazyBackgroundData.style.backgroundImage = 'url(' + lazyBackgroundData.dataset.bg + ')';
		});
	}
});