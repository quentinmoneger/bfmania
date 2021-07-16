
$( "#container" ).click(function() {
    var test = document.querySelector('#score .ones li');
    var result   = getComputedStyle(test, ':before').content;
    console.log(result)
});