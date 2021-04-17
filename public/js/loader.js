// Vous pouvez changer la vitesse (speed) (1 == tres rapide)
// Vous pouvez changer la qualité de la transition (1 == best)
// Plus la transition est de qualité, plus la rapidité ralenti et doit être augmenté
var speed = 10;
var transition = 10;


var timer= 0;
var opaciT = 100; 

function opacity()
{
    opaciT = opaciT - transition;

    var object = document.getElementById("preloader").style;
    object.opacity = (opaciT / 100);
    object.MozOpacity = (opaciT / 100);
    object.KhtmlOpacity = (opaciT / 100);
    object.filter = "alpha(opacity=" + opaciT + ")"; 

	if (opaciT <= 0)
	{
		document.getElementById("preloader").style.visibility="hidden";
		clearInterval(timer);
	}

}

function preload()
{
	if (document.getElementById)
	{
		document.getElementById("preloadIMG").style.visibility="hidden";
		timer = setInterval("opacity()",speed);
	}

	else
	{
	   	if (document.layers)
		{
			document.preloadIMG.visibility = "hidden";
			timer = setInterval("opacity()",speed);
	    }
	        else
		{
			document.all.preloadIMG.style.visibility = "hidden";
			timer = setInterval("opacity()",speed);
		}
	}
}

// GÉNÉRER LE CSS POUR LE PRELOADER
var myCSS;
myCSS = '<style type="text/css">';

myCSS += "#preloader {";
myCSS += "position:fixed;";
myCSS += "background-color:black;";
myCSS += "width:100%;";
myCSS += "height:100%; ";
myCSS += "display:block;";
myCSS += "z-index:100000;";
myCSS += "}";

myCSS += "#preloadIMG {";
myCSS += "width:100%;";
myCSS += "height:100%; ";
myCSS += "margin-top:150px";
myCSS += "}";

myCSS += "</style>";

// ÉCRITURE DES CSS
window.document.write(myCSS);

// ÉXÉCUTION
window.onload = function() { preload(); }