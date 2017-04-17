//Маска для времени 
jQuery(function($) {
	$.mask.definitions['~']='[:]';
	$('#time').mask('99:99:99');
});

/*//autocomplete
// массив строк
var availableTags = [
  "Испанский",
  "Итальянский",
  "Английский",
  "Китайский",
  "Русский"
];
// задаем массив в качестве источника слов для автозаполнения.
$(function(){
	$( "#tags" ).autocomplete({
	source: availableTags,
	minLength: 2});
	});*/

//При нажатии ссылки "Версия для печати"
function print_onclick() {
    window.print();
    return false;
}