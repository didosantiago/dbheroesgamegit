var DBH = DBH || {};

DBH.common = {
	$body: $('body'),
	bxProduct: {},
	bxProducThumb: {},
	owlProductMobile: {},
	createCrossDomainRequest: function(url, handler) {
		var request,
			isIE8 = window.XDomainRequest ? true : false

		if (isIE8) {
			request = new window.XDomainRequest();
		} else {
			request = new XMLHttpRequest();
		}
		return request;
	},
	removeAccents: function(newStringComAcento) {
		var string = newStringComAcento;
		var mapaAcentosHex = {
			a: /[\xE0-\xE6]/g,
			e: /[\xE8-\xEB]/g,
			i: /[\xEC-\xEF]/g,
			o: /[\xF2-\xF6]/g,
			u: /[\xF9-\xFC]/g,
			c: /\xE7/g,
			n: /\xF1/g
		};

		for (var letra in mapaAcentosHex) {
			var expressaoRegular = mapaAcentosHex[letra];
			string = string.replace(expressaoRegular, letra);
		}

		return string.replace(/\(?\d\)?/g, '').trim().replace(/\s|\./g, '-').toLowerCase(); //retira o "(numero)"
	},
	remove_class: function(element, _regex) {
		var regex = new RegExp(_regex);
		element.removeClass(function(index, classNames) {
			var current_classes = classNames.split(' '),
				font_remove = [];

			$.each(current_classes, function(index, item_class) {
				if (regex.test(item_class)) {
					font_remove.push(item_class);
				}
			});

			return font_remove.join(' ');
		});
	},
	formatCurrency: function(int) {
		var tmp = int + '';
		tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
		if (tmp.length > 6)
			tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

		return 'R$ ' + tmp;
	},
	sortUsingNestedText: function(parent, childSelector, keySelector) {
        var items = parent.children(childSelector).sort(function(a, b) {
            var vA = $(keySelector, a).text();
            var vB = $(keySelector, b).text();
            return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
        });
        parent.append(items);
    },
    justNumbers: function(string) {
	    var numsStr = string.replace(/[^0-9]/g,'');
	    return parseInt(numsStr);
    }
}