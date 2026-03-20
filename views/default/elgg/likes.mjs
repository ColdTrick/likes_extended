import 'jquery';
import Ajax from 'elgg/Ajax';
import hooks from 'elgg/hooks';
import popup from 'elgg/popup';

function update_like_menu_items(guid, menu_items) {
	$.each(menu_items, function (index, elem) {
		$('li[data-menu-item="' + index + '"] a[data-likes-guid=' + guid + ']').each(function() {
			const $target = $(this).data('dropdownMenu');
			if ($target !== undefined) {
				let $link = $(elem).filter('a');
				
				$(this).removeClass('elgg-state-selected');
				$(this).addClass($link.attr('class'));
				$(this).html($link.html());
			} else {
				$(this).replaceWith(elem);
			}
		});
	});
}

function set_counts(guid, num_likes) {
	const li_modifier = num_likes > 0 ? 'removeClass' : 'addClass';

	$('.elgg-menu-item-likes-count > a[data-likes-guid=' + guid + ']').each(function () {
		$(this).parent()[li_modifier]('hidden');
	});
}

$(document).on('click', 'li[data-menu-item^="likes_"][data-menu-item!="likes_count"][data-menu-item!="likes_dropdown"][data-menu-item!="likes_stats"] a', function () {
	let ajax = new Ajax();
	const $parent_menu = $(this).closest('.elgg-menu'),
		menu_item_name = $(this).closest('li').data().menuItem;
	
	ajax.action($(this).prop('href'), {
		success: function() {
			$parent_menu.find('li[data-menu-item="' + menu_item_name + '"] > a').focus();
			popup.close();
		}
	});

	return false;
});

// Any Ajax operation can return likes data
hooks.register(Ajax.RESPONSE_DATA_HOOK, 'all', function (hook, type, params, value) {
	if (value.likes_status) {
		const status = value.likes_status;
		update_like_menu_items(status.guid, status.menu);
		set_counts(status.guid, status.count);
	}
});
