"use strict";

function _createForOfIteratorHelper(o, allowArrayLike) {
	var it;
	if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
		if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
			if (it) o = it;
			var i = 0;
			var F = function F() {};
			return {
				s: F,
				n: function n() {
					if (i >= o.length) return {
						done: true
					};
					return {
						done: false,
						value: o[i++]
					};
				},
				e: function e(_e) {
					throw _e;
				},
				f: F
			};
		}
		throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}
	var normalCompletion = true,
		didErr = false,
		err;
	return {
		s: function s() {
			it = o[Symbol.iterator]();
		},
		n: function n() {
			var step = it.next();
			normalCompletion = step.done;
			return step;
		},
		e: function e(_e2) {
			didErr = true;
			err = _e2;
		},
		f: function f() {
			try {
				if (!normalCompletion && it.return != null) it.return();
			} finally {
				if (didErr) throw err;
			}
		}
	};
}

function _unsupportedIterableToArray(o, minLen) {
	if (!o) return;
	if (typeof o === "string") return _arrayLikeToArray(o, minLen);
	var n = Object.prototype.toString.call(o).slice(8, -1);
	if (n === "Object" && o.constructor) n = o.constructor.name;
	if (n === "Map" || n === "Set") return Array.from(o);
	if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}

function _arrayLikeToArray(arr, len) {
	if (len == null || len > arr.length) len = arr.length;
	for (var i = 0, arr2 = new Array(len); i < len; i++) {
		arr2[i] = arr[i];
	}
	return arr2;
}

function ownKeys(object, enumerableOnly) {
	var keys = Object.keys(object);
	if (Object.getOwnPropertySymbols) {
		var symbols = Object.getOwnPropertySymbols(object);
		if (enumerableOnly) symbols = symbols.filter(function (sym) {
			return Object.getOwnPropertyDescriptor(object, sym).enumerable;
		});
		keys.push.apply(keys, symbols);
	}
	return keys;
}

function _objectSpread(target) {
	for (var i = 1; i < arguments.length; i++) {
		var source = arguments[i] != null ? arguments[i] : {};
		if (i % 2) {
			ownKeys(Object(source), true).forEach(function (key) {
				_defineProperty(target, key, source[key]);
			});
		} else if (Object.getOwnPropertyDescriptors) {
			Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
		} else {
			ownKeys(Object(source)).forEach(function (key) {
				Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
			});
		}
	}
	return target;
}

function _defineProperty(obj, key, value) {
	if (key in obj) {
		Object.defineProperty(obj, key, {
			value: value,
			enumerable: true,
			configurable: true,
			writable: true
		});
	} else {
		obj[key] = value;
	}
	return obj;
}
var JSCCommon = {
	// часть вызов скриптов здесь, для использования при AJAX
	btnToggleMenuMobile: [].slice.call(document.querySelectorAll(".toggle-menu-mobile--js")),
	menuMobile: document.querySelector(".menu-mobile--js"),
	menuMobileLink: [].slice.call(document.querySelectorAll(".menu-mobile--js ul li a")),
	modalCall: function modalCall() {
		$(".link-modal").fancybox({
			arrows: false,
			infobar: false,
			touch: false,
			type: 'inline',
			autoFocus: false,
			i18n: {
				en: {
					CLOSE: "Закрыть",
					NEXT: "Вперед",
					PREV: "Назад" // PLAY_START: "Start slideshow",
					// PLAY_STOP: "Pause slideshow",
					// FULL_SCREEN: "Full screen",
					// THUMBS: "Thumbnails",
					// DOWNLOAD: "Download",
					// SHARE: "Share",
					// ZOOM: "Zoom"

				}
			}
		});
		$(".modal-close-js").click(function () {
			$.fancybox.close();
		});
		$.fancybox.defaults.backFocus = false;
		var linkModal = document.querySelectorAll('.link-modal');

		function addData() {
			linkModal.forEach(function (element) {
				element.addEventListener('click', function () {
					var modal = document.querySelector(element.getAttribute("href"));
					var data = element.dataset;

					function setValue(val, elem) {
						if (elem && val) {
							var el = modal.querySelector(elem);
							el.tagName == "INPUT" ? el.value = val : el.innerHTML = val; // console.log(modal.querySelector(elem).tagName)
						}
					}

					setValue(data.title, '.ttu');
					setValue(data.text, '.after-headline');
					setValue(data.btn, '.btn');
					setValue(data.order, '.order');
				});
			});
		}

		if (linkModal) addData();
	},
	// /modalCall
	toggleMenu: function toggleMenu() {
		var _this2 = this;

		if (this.btnToggleMenuMobile) {
			this.btnToggleMenuMobile.forEach(function (element) {
				element.addEventListener('click', function () {
					_this2.btnToggleMenuMobile.forEach(function (element) {
						return element.classList.toggle("on");
					});

					_this2.menuMobile.classList.toggle("active");

					document.body.classList.toggle("fixed");
					return false;
				});
			});
		}
	},
	closeMenu: function closeMenu() {
		if (this.menuMobile) {
			this.btnToggleMenuMobile.forEach(function (element) {
				element.classList.remove("on");
			});
			this.menuMobile.classList.remove("active");
			document.body.classList.remove("fixed");
		}
	},
	mobileMenu: function mobileMenu() {
		var _this3 = this;

		if (this.menuMobileLink) {
			this.toggleMenu();
			document.addEventListener('mouseup', function (event) {
				var container = event.target.closest(".menu-mobile--js.active"); // (1)

				if (!container) {
					_this3.closeMenu();
				}
			}, {
				passive: true
			});
			window.addEventListener('resize', function () {
				if (window.matchMedia("(min-width: 992px)").matches) {
					JSCCommon.closeMenu();
				}
			}, {
				passive: true
			});
		}
	},
	// /mobileMenu
	// табы  .
	tabscostume: function tabscostume(tab) {
		var tabs = {
			Btn: [].slice.call(document.querySelectorAll(".".concat(tab, "__btn"))),
			BtnParent: [].slice.call(document.querySelectorAll(".".concat(tab, "__caption"))),
			Content: [].slice.call(document.querySelectorAll(".".concat(tab, "__content")))
		};
		tabs.Btn.forEach(function (element, index) {
			element.addEventListener('click', function () {
				if (!element.classList.contains('active')) {
					var siblings = element.parentNode.querySelector(".".concat(tab, "__btn.active"));
					var siblingsContent = tabs.Content[index].parentNode.querySelector(".".concat(tab, "__content.active"));
					siblings.classList.remove('active');
					siblingsContent.classList.remove('active');
					element.classList.add('active');
					tabs.Content[index].classList.add('active');
				}
			});
		}); // $('.' + tab + '__caption').on('click', '.' + tab + '__btn:not(.active)', function (e) {
		// 	$(this)
		// 		.addClass('active').siblings().removeClass('active')
		// 		.closest('.' + tab).find('.' + tab + '__content').hide().removeClass('active')
		// 		.eq($(this).index()).fadeIn().addClass('active');
		// });
	},
	// /табы
	// /inputMask
	customRange: function customRange() {
		$(".range-wrap").each(function () {
			var _this = $(this);

			var $range = _this.find(".slider-js");

			var $inputFrom = _this.find(".input_from");

			var $inputTo = _this.find(".input_to");

			var instance,
				from,
				to,
				min = $range.data('min'),
				max = $range.data('max');
			$range.ionRangeSlider({
				skin: "round",
				type: "double",
				grid: false,
				grid_snap: false,
				hide_min_max: true,
				hide_from_to: true,
				onStart: updateInputs,
				onChange: updateInputs,
				onFinish: updateInputs
			});
			instance = $range.data("ionRangeSlider");

			function updateInputs(data) {
				from = data.from;
				to = data.to;
				$inputFrom.prop("value", from);
				$inputTo.prop("value", to);
			}

			$inputFrom.on("change", function () {
				var val = $(this).prop("value"); // validate

				if (val < min) {
					val = min;
				} else if (val > to) {
					val = to;
				}

				instance.update({
					from: val
				});
				$(this).prop("value", val);
			});
			$inputTo.on("change", function () {
				var val = $(this).prop("value"); // validate

				if (val < from) {
					val = from;
				} else if (val > max) {
					val = max;
				}

				instance.update({
					to: val
				});
				$(this).prop("value", val);
			});
		});
	},
	ifie: function ifie() {
		var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;

		if (isIE11) {
			$("body").prepend('<p   class="browsehappy container">К сожалению, вы используете устаревший браузер. Пожалуйста, <a href="http://browsehappy.com/" target="_blank">обновите ваш браузер</a>, чтобы улучшить производительность, качество отображаемого материала и повысить безопасность.</p>');
		}
	},
	heightwindow: function heightwindow() {
		// First we get the viewport height and we multiple it by 1% to get a value for a vh unit
		var vh = window.innerHeight * 0.01; // Then we set the value in the --vh custom property to the root of the document

		document.documentElement.style.setProperty('--vh', "".concat(vh, "px")); // We listen to the resize event

		window.addEventListener('resize', function () {
			// We execute the same script as before
			var vh = window.innerHeight * 0.01;
			document.documentElement.style.setProperty('--vh', "".concat(vh, "px"));
		}, {
			passive: true
		});
	},
	animateScroll: function animateScroll() {
		// листалка по стр
		$(" .top-nav li a, .scroll-link").click(function () {
			var elementClick = $(this).attr("href");
			var destination = $(elementClick).offset().top;
			$('html, body').animate({
				scrollTop: destination
			}, 1100);
			return false;
		});
	}
};
var $ = jQuery;

function eventHandler() {
	var _defaultSl;

	JSCCommon.modalCall();
	JSCCommon.tabscostume('tabs');
	JSCCommon.mobileMenu();
	JSCCommon.ifie();
	JSCCommon.heightwindow();
	JSCCommon.animateScroll();
	JSCCommon.customRange(); // JSCCommon.CustomInputFile();

	function whenResize() {
		var topH = document.querySelector('header').scrollHeight;
		var stickyElement = document.querySelector('.top-nav');
		if (!stickyElement) return;

		window.onscroll = function () {
			if ($(window).scrollTop() > topH) {
				stickyElement.classList.add('fixed');
			} else {
				stickyElement.classList.remove('fixed');
			}
		};
	}

	window.addEventListener('resize', function () {
		whenResize();
	}, {
		passive: true
	});
	whenResize();
	$('.sDescr__showMore').click(function () {
		$(this).slideToggle();
		$(this).parent().find('.sDescr__row').addClass('active');
	});
	var defaultSl = (_defaultSl = {
		spaceBetween: 0,
		lazy: {
			loadPrevNext: true
		},
		watchOverflow: true
	}, _defineProperty(_defaultSl, "spaceBetween", 0), _defineProperty(_defaultSl, "loop", true), _defineProperty(_defaultSl, "navigation", {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev'
	}), _defineProperty(_defaultSl, "pagination", {
		el: ' .swiper-pagination',
		type: 'bullets',
		clickable: true // renderBullet: function (index, className) {
		// 	return '<span class="' + className + '">' + (index + 1) + '</span>';
		// }

	}), _defaultSl);
	var bannerSleder = new Swiper('.bannerSleder-js', {
		slidesPerView: 1,
		loop: true,
		lazy: {
			loadPrevNext: true,
			loadPrevNextAmount: 2
		},
		pagination: {
			el: '.bannerSleder-js .swiper-pagination',
			clickable: true
		}
	});
	var sliderCategory = new Swiper('.sliderCategory-js', {
		slidesPerView: 2,

		loop: false,
		freeModeMomentum: true,
		watchOverflow: true,
		spaceBetween: 3,
		lazy: {
			loadPrevNext: true,
			loadPrevNextAmount: 20
		},
		pagination: {
			el: '.sliderCategory-js .swiper-pagination',
			clickable: true
		},
		breakpoints: {
			992: {
				slidesPerView: 'auto'
			}
		}
	});
	var swiper4 = new Swiper('.sBanners__slider--js', _objectSpread(_objectSpread({}, defaultSl), {}, {
		slidesPerView: 'auto',
		freeMode: true,
		loopFillGroupWithBlank: true,
		touchRatio: 0.2,
		slideToClickedSlide: true,
		freeModeMomentum: true
	}));
	$('.productItem__heart').click(function () {
		$(this).toggleClass('active');
	});
	$('.accardion-js').click(function () {
		$(this).toggleClass('active');
		$(this).parent().find('.accardion-toggle-js').slideToggle();
	});
	var now = new Date();
	$('.curentYear').text(now.getFullYear()); //luckyoneJs
	//prod card slider

	var prodCardThumb = new Swiper('.prod-card-thumb-js', {
		slidesPerView: 'auto',
		spaceBetween: 5,
		//breakpoints
		breakpoints: {
			10: {
				direction: 'vertical'
			},
			576: {
				direction: 'horizontal'
			},
			768: {},
			992: {}
		},
		//nav
		navigation: {
			nextEl: '.prod-card-th-next',
			prevEl: '.prod-card-th-prev'
		},
		lazy: {
			loadPrevNext: true,
			loadPrevNextAmount: 10
		}
	});
	var prodCardSlider = new Swiper('.prod-card-slider-js', {
		slidesPerView: 1,
		spaceBetween: 20,
		loop: true,
		thumbs: {
			swiper: prodCardThumb
		},
		lazy: {
			loadPrevNext: true,
			loadPrevNextAmount: 2
		}
	}); //.det-slider-js

	var detSlider = new Swiper('.det-slider-js', {
		slidesPerView: 'auto',
		spaceBetween: 10,
		loop: true,
		//nav
		navigation: {
			nextEl: '.det-slider-next',
			prevEl: '.det-slider-prev'
		},
		//lazy
		lazy: {
			loadPrevNext: true,
			loadPrevNextAmount: 4
		}
	});
	$('.prod-descr-tab-js').click(function () {
		window.setTimeout(function () {
			var detSwiperCont = document.querySelector('.det-slider-js');
			if (!detSwiperCont) return;
			detSwiperCont.swiper.update();
		}, 300);
	});

	//.buy-with-slider-js
	var buyWithSlider = new Swiper('#RelatedSlider', {
		//loop: true,
		slidesPerColumnFill: 'row',
		breakpoints: {
			1: {
				spaceBetween: 15,
				slidesPerView: 2,
				slidesPerColumn: 2
			},
			575: {
				slidesPerColumn: 2,
				slidesPerView: 2,
				spaceBetween: 15
			},
			768: {
				slidesPerView: 3,
				slidesPerColumn: 1,
				spaceBetween: 20
			},
			1200: {
				slidesPerView: 4,
				slidesPerColumn: 1,
				spaceBetween: 30
			}
		},
		//pagination
		pagination: {
			el: $(this).find('.buy-with-pugin'),
			clickable: true
		},
		//nav
		navigation: {
			nextEl: $('.buy-with-next', $('#RelatedSliderWrap')),
			prevEl: $('.buy-with-prev', $('#RelatedSliderWrap'))
		},
		lazy: {
			loadPrevNext: true,
			loadPrevNextAmount: 4
		}
	});
	$('.modalBasket-js').click(function () {
		buyWithSlider.update();
	});

	//stars js

	$('.star-js').click(function () {
		var thisIndex = $('.star-js').index(this);
		var allThis = this.parentElement.children;
		$('.star-js').children('[name="rating"]').prop('checked', true);
		for (var i = 0; i <= thisIndex; i++) {
			allThis[i].classList.add('active');
		}
		for (var _i = thisIndex + 1; _i <= allThis.length - 1; _i++) {
			allThis[_i].classList.remove('active');
		}
	});

	$('.star-js').mouseover(function () {
		var thisIndex = $('.star-js').index(this);
		var allThis = this.parentElement.children;
		for (var i = 0; i <= thisIndex; i++) {
			allThis[i].classList.add('hover-paint-js');
		}
		for (var _i2 = thisIndex + 1; _i2 <= allThis.length - 1; _i2++) {
			allThis[_i2].classList.remove('hover-paint-js');
		}
	});
	$('.star-js').mouseout(function () {
		var allThis = this.parentElement.children;
		var _iterator = _createForOfIteratorHelper(allThis),
			_step;

		try {
			for (_iterator.s(); !(_step = _iterator.n()).done;) {
				var star = _step.value;
				star.classList.remove('hover-paint-js');
			}
		} catch (err) {
			_iterator.e(err);
		} finally {
			_iterator.f();
		}
	});
};

if (document.readyState !== 'loading') {
	eventHandler();
} else {
	document.addEventListener('DOMContentLoaded', eventHandler);
}