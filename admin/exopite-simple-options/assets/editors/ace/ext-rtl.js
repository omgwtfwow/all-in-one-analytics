ace.define("ace/ext/rtl", ["require", "exports", "module", "ace/lib/dom", "ace/lib/lang", "ace/editor", "ace/config"], function (e, t, n) {
	"use strict";

	function u(e, t) {
		var n = t.getSelection().lead;
		t.session.$bidiHandler.isRtlLine(n.row) && n.column === 0 && (t.session.$bidiHandler.isMoveLeftOperation && n.row > 0 ? t.getSelection().moveCursorTo(n.row - 1, t.session.getLine(n.row - 1).length) : t.getSelection().isEmpty() ? n.column += 1 : n.setPosition(n.row, n.column + 1))
	}

	function a(e) {
		e.editor.session.$bidiHandler.isMoveLeftOperation = /gotoleft|selectleft|backspace|removewordleft/.test(e.command.name)
	}

	function f(e, t) {
		t.$bidiHandler.currentRow = null;
		if (t.$bidiHandler.isRtlLine(e.start.row) && e.action === "insert" && e.lines.length > 1) for (var n = e.start.row; n < e.end.row; n++) t.getLine(n + 1).charAt(0) !== t.$bidiHandler.RLE && (t.getDocument().$lines[n + 1] = t.$bidiHandler.RLE + t.getLine(n + 1))
	}

	function l(e, t) {
		var n = t.session, r = n.$bidiHandler, i = t.$textLayer.$lines.cells,
			s = t.layerConfig.width - t.layerConfig.padding + "px";
		i.forEach(function (e) {
			var t = e.element.style;
			r && r.isRtlLine(e.row) ? (t.direction = "rtl", t.textAlign = "right", t.width = s) : (t.direction = "", t.textAlign = "", t.width = "")
		})
	}

	function c(e) {
		function n(e) {
			var t = e.element.style;
			t.direction = t.textAlign = t.width = ""
		}

		var t = e.$textLayer.$lines;
		t.cells.forEach(n), t.cellCache.forEach(n)
	}

	var r = e("ace/lib/dom"), i = e("ace/lib/lang"), s = [{
		name: "leftToRight", bindKey: {win: "Ctrl-Alt-Shift-L", mac: "Command-Alt-Shift-L"}, exec: function (e) {
			e.session.$bidiHandler.setRtlDirection(e, !1)
		}, readOnly: !0
	}, {
		name: "rightToLeft", bindKey: {win: "Ctrl-Alt-Shift-R", mac: "Command-Alt-Shift-R"}, exec: function (e) {
			e.session.$bidiHandler.setRtlDirection(e, !0)
		}, readOnly: !0
	}], o = e("../editor").Editor;
	e("../config").defineOptions(o.prototype, "editor", {
		rtlText: {
			set: function (e) {
				e ? (this.on("session", f), this.on("changeSelection", u), this.renderer.on("afterRender", l), this.commands.on("exec", a), this.commands.addCommands(s)) : (this.off("session", f), this.off("changeSelection", u), this.renderer.off("afterRender", l), this.commands.off("exec", a), this.commands.removeCommands(s), c(this.renderer)), this.renderer.updateFull()
			}
		}
	})
});
(function () {
	ace.require(["ace/ext/rtl"], function (m) {
		if (typeof module == "object" && typeof exports == "object" && module) {
			module.exports = m;
		}
	});
})();
            