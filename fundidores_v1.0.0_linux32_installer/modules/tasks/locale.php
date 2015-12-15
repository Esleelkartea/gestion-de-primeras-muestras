<?php
setlocale(LC_TIME, "C");
echo strftime("%A");
setlocale(LC_TIME, "fi_FI");
echo strftime(" en Finland&eacute;s es %A,");
setlocale(LC_TIME, "fr_FR");
echo strftime(" en Franc&eacute;s %A y");
setlocale(LC_TIME, "de_DE");
echo strftime(" en Alem&aacute;n %A.\n");
?> 