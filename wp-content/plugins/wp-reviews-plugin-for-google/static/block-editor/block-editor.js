!function(e) {
	var t = {};
	function n(l) {
		if (t[l])
			return t[l].exports;
		var i = t[l] = {
			i: l,
			l: !1,
			exports: {}
		};
		return e[l].call(i.exports, i, i.exports, n),
		i.l = !0,
		i.exports
	}
	n.m = e,
	n.c = t,
	n.d = function(e, t, l) {
		n.o(e, t) || Object.defineProperty(e, t, {
			enumerable: !0,
			get: l
		})
	},
	n.r = function(e) {
		"undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {
			value: "Module"
		}),
		Object.defineProperty(e, "__esModule", {
			value: !0
		})
	},
	n.t = function(e, t) {
		if (1 & t && (e = n(e)),
		8 & t)
			return e;
		if (4 & t && "object" == typeof e && e && e.__esModule)
			return e;
		var l = Object.create(null);
		if (n.r(l),
		Object.defineProperty(l, "default", {
			enumerable: !0,
			value: e
		}),
		2 & t && "string" != typeof e)
			for (var i in e)
				n.d(l, i, function(t) {
					return e[t]
				}
				.bind(null, i));
		return l
	},
	n.n = function(e) {
		var t = e && e.__esModule ? function() {
			return e.default
		}
		: function() {
			return e
		}
		;
		return n.d(t, "a", t),
		t
	},
	n.o = function(e, t) {
		return Object.prototype.hasOwnProperty.call(e, t)
	},
	n.p = "",
	n(n.s = 1)
}([
	function(e, t) {
		e.exports = window.wp.components
	}, function(e, t, n) {
		"use strict";
		n.r(t);
		var l = n(0)
		, i = wp.blocks.registerBlockType
		, c = function() {
			return React.createElement(l.Icon, {
				icon: React.createElement("svg", {
					xmlns: "http://www.w3.org/2000/svg",
					viewBox: "0 0 48 48"
				}, React.createElement("g", null,
					React.createElement("circle", {
						cx: "24",
						cy: "24",
						r: "24",
						fillRule: "evenodd",
						clipRule: "evenodd",
						fill: "#1A976A"
					}), React.createElement("path", {
						d: "M23,23.3l9-9c0.7-0.7,1.8-0.7,2.4,0l2.4,2.4c0.7,0.7,0.7,1.8,0,2.4l-12,12c0,0-0.1,0.1-0.1,0.1l-2.4,2.4 c-0.3,0.3-0.8,0.5-1.2,0.5c-0.4,0-0.9-0.2-1.2-0.5l-2.4-2.4c0,0-0.1-0.1-0.1-0.1l-6.3-6.3c-0.7-0.7-0.7-1.7,0-2.4l2.4-2.4 c0.7-0.7,1.8-0.7,2.4,0l4.7,4.7l0.4,0.4L26,30L23,23.3L23,23.3z",
						fillRule: "evenodd",
						clipRule: "evenodd",
						fill: "#FFFFFF"
					})
				))
			})
		};
		i("trustindex/block-selector", {
			title: "Trustindex",
			description: "Trustindex widget plugin",
			icon: c(),
			category: "widgets",
			attributes: {
				widget_id: {
					type: "string"
				},
				trustindex_widgets: {
					type: "object"
				},
				free_widgets: {
					type: "object"
				},
				setup_url: {
					type: "string"
				},
				selected_type: {
					type: "string",
					default: "admin"
				}
			},
			edit: function(e) {
				var t = e.attributes
				, n = e.setAttributes;
				function i(e) {
					"type-select" === e.target.id && "custom-id" === e.target.selectedOptions[0].id ? (n({
						selected_type: "custom"
					}),
					n({
						widget_id: ""
					})) : "type-select" === e.target.id ? (n({
						widget_id: e.target.value
					}),
					e.target.value.length >= 20 ? n({
						selected_type: "admin"
					}) : n({
						selected_type: "free"
					})) : n({
						widget_id: e.target.value
					})
				}
				return t.trustindex_widgets || wp.apiFetch({
					path: "trustindex/v1/setup-complete"
				}).then((function(e) {
					e.result && n({
						free_widgets: e.result
					}),
					e.setup_url && n({
						setup_url: e.setup_url
					}),
					wp.apiFetch({
						path: "trustindex/v1/get-widgets"
					}).then((function(e) {
						n({
							trustindex_widgets: e
						}),
						e && e.length || "admin" !== t.selected_type || n({
							selected_type: "custom"
						}),
						e && e.length && !t.widget_id && n({
							widget_id: e[0].widgets[0].id
						})
					}
					))
				}
				)),
				t.trustindex_widgets ? React.createElement("div", {
					className: "components-placeholder"
				}, React.createElement("table", null, React.createElement("tr", null, React.createElement("td", {style:{padding:"0",paddingBottom:"15px"}}, React.createElement("span", {
					style: {
						fontSize: "32px"
					}
				}, c(), " Trustindex widget"))), React.createElement("tr", null, React.createElement("td", {style:{padding:"0"}}, React.createElement("select", {
					id: "type-select",
					style: {
						fontSize: "13px",
						border: "1px solid",
						width: "100%",
						maxWidth: "none"
					},
					onChange: i
				}, React.createElement("optgroup", {
					label: "Trustindex widgets"
				}, t.trustindex_widgets.length ? t.trustindex_widgets[0].widgets.map((function(e) {
					return React.createElement("option", {
						selected: t.widget_id === e.id,
						value: e.id
					}, e.name)
				}
				)) : React.createElement("option", {
					disabled: !0
				}, " No Trustindex account connected ")), React.createElement("optgroup", {
					label: "Custom widget"
				}, React.createElement("option", {
					selected: "custom" === t.selected_type,
					value: t.widget_id,
					id: "custom-id"
				}, "Custom widget id")), React.createElement("optgroup", {
					label: "Free widgets"
				}, Object.keys(t.free_widgets).map((function(e) {
					return React.createElement("option", {
						disabled: 0 === t.free_widgets[e],
						selected: e === t.widget_id,
						value: e
					}, "Free " + e + " review widget" + (0 === t.free_widgets[e] ? " - not configured yet" : ""))
				}
				)))))), React.createElement("tr", null, React.createElement("td", {style:{padding:"0"}}, React.createElement("input", {
					onChange: i,
					value: t.widget_id,
					style: {
						border: "1px solid black",
						display: "custom" === t.selected_type ? "inherit" : "none",
						marginTop: "10px"
					},
					class: "block-editor-plain-text blocks-shortcode__textarea",
					type: "text"
				})))),
					React.createElement("p", {
						style: {
							fontSize: "15px",
							marginBottom: "0px"
						}
					},
					t.trustindex_widgets.length ? "" : React.createElement("p", {style:{marginBottom:"0px"}}, "If you have a Trustindex account, connect it to access the widgets in your account as well."), React.createElement("a", {
						target: "_blank",
						href: t.setup_url
					}, "Connect account"))
				) : React.createElement("div", {
					class: "components-placeholder"
				}, "Loading...")
			},
			save: function(e) {
				return e.attributes,
				null
			}
		})
	}
]);
