(function(global, factory) {
	// AMD
	if (typeof define === 'function' && define.amd) {
		define([ 'jquery' ], factory);
	// CommonJS/Browserify
	} else if (typeof exports === 'object') {
		factory(require('jquery'));
	// Global
	} else {
		factory(global.jQuery);
	}
}(this, function($) {
	'use strict';

	 
	$.fn.toggleswitch = function() {
 
        this.filter( "input[type='checkbox']" ).each(function() {
            var checkbox = $( this );
			var label = $('<label class="switch"></label>');
            checkbox.before(label);
			label.append(checkbox).append('<div class="slider round"></div>');
        });
	};
	 

}));