$(".cart-del-button").click(function() {
	
})

$(".cart-del-button").hover(function() {
		$(this).css("color", "#7c7e82");
	}, function() {
		$(this).css("color", "#b0b2b7");
	}
)

var sc35_changeSubtotal = function(newVal) {
	$("#subtotal-value").html(newVal);
}


// handler checkbox bundle
$("#cart-form").on('click', '.item-bundle-header .cart-checkbox', function() {
	var listContainer = $(this).parent().parent().children(".item-list");
	var value = ($(this).is(':checked'));
	listContainer.find(".cart-checkbox").each(function() {
		$(this).prop('checked', value)
	})
})

$(document).ready(function() {
})