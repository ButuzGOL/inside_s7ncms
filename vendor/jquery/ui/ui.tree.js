(function($) {

$.widget("ui.tree", {
	_init: function() {

		var self = this;
		this.identifier = (new Date()).getTime()+Math.random();

		this.element.sortable({
			items: this.options.sortOn,
			scope: this.identifier,
			distance: this.options.distance,
			placeholder: "ui-tree-placeholder",
			helper: this.options.helper,
			handle: this.options.handle,
			scroll: this.options.scroll,
			appendTo: this.options.appendTo,
			opacity: 0.8,
			
			start: function(event, ui) {
				var inst = $(this).data("sortable");
				inst.placeholder.hide();
				inst.helperProportions.height = inst.currentItem.find(self.options.dropOn).length ? inst.currentItem.find(self.options.dropOn).outerHeight() : inst.currentItem.outerHeight();
				inst._preserveHelperProportions = true;
				inst.refreshPositions(true);

				self.originalParent = ui.item.parent();

				(self.options.start && self.options.start.apply(this, [event, ui]));
			},
			
			stop: function(event, ui) {
				if ( self.originalParent.is(':empty') )
					self.originalParent.remove();

				(self.options.stop && self.options.stop.apply(this, [event, ui]));
			}
		});

		//Make certain nodes droppable
		$(this.options.dropOn, this.element).droppable({
			accept: this.options.sortOn,
			hoverClass: this.options.dropHoverClass,
			tolerance: "pointer",
			scope: this.identifier,
			over: function(event, ui) {
				$(self.options.sortOn, self.element).removeClass(self.options.sortIndicatorDown).removeClass(self.options.sortIndicatorUp);
				self.overDroppable = true;
				self._trigger('over', event, ui);
			},
			out: function(event, ui) {
				self.overDroppable = false;
				(self.options.out && self.options.out.apply(this, [event, ui]));
			},
			drop: function(event, ui) {
				var ul = $(this).parent().find("ul");
				if(!ul.length) ul = $("<ul></ul>").appendTo($(this).parent());

				ui.draggable.appendTo( $(this).parent().find("> ul") );

				self.element.data("sortable")._noFinalSort = true;

				(self.options.drop && self.options.drop.apply(this, [event, ui]));
			}
		});

	},
	
	serialize: function(o) {
		return this.toJSON($(' > li', this.element));
	},
	
	toJSON: function(elements) {
		var serialized = [];
		var _this = this;
		
		elements.each(function(i, el){
			if ($('> ul', el).length > 0)
				serialized.push('"' + el.id + '":' + _this.toJSON($(' > ul > li', el)));
			else
				serialized.push('"'+el.id+'":{}');
		});
		
		return '{'+serialized.join(',')+'}';
	}
});

$.extend($.ui.tree, {
	getter: "serialize",
	defaults: {
		sortOn: "*",
		dropOn: "div",
		dropHoverClass: "ui-tree-hover"
	}
});

})(jQuery);