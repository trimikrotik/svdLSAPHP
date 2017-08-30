<?php
//include("tokenizing.php");
echo "<!DOCTYPE html>\n"; 
echo "<html>\n"; 
echo "<head>\n"; 
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"; 
echo "<title>Ganong.com | Search Engine Pencari Jurnal</title>\n"; 
echo "\n"; 
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"; 
echo "\n"; 
echo "</head>\n"; 
echo "\n"; 
echo "<body>\n"; 
echo "\n"; 
echo "<div id=\"page\">\n"; 
echo "\n"; 
echo "    <h1>Ganong.com</h1>\n"; 
echo "    \n"; 
//echo "    <form id=\"searchForm\" method=\"post\" action=\"search_exe2_2.php\">\n";
echo "    <form id=\"searchForm\" method=\"post\" action=\"rumus_metode.php\">\n";
/*untuk modifikasi saja*/
//echo "    <form id=\"searchForm\" method=\"post\" action=\"hasilsearch.php\">\n";
/*batas modifikasi*/

echo "		<fieldset>\n"; 
echo "        \n"; 
echo "           	<input id=\"s\" type=\"text\" name=\"cari\"/>\n"; 
echo "            \n"; 
echo "            <input type=\"submit\" value=\"Submit\" id=\"submitButton\" />\n"; 
echo "            \n"; 
echo "            <div id=\"searchInContainer\">              \n"; 
//echo "                <input type=\"radio\" name=\"check\" value=\"web\" id=\"searchWeb\" />\n"; 
//echo "                <label for=\"searchWeb\">Search document</label>\n"; 
echo "			</div>                           \n"; 
echo "        </fieldset>\n"; 
echo "    </form>\n"; 
echo "\n"; 
echo "    <div id=\"resultsDiv\"></div>\n"; 
echo "    \n"; 
echo "</div>\n"; 
echo "\n"; 
echo "<!-- It would be great if you leave the link back to the tutorial. Thanks! -->\n"; 
echo "<p class=\"credit\"><a href=\"\">created by Tri Setiawan ITATS - 06.2010.1.05467</a></p>\n"; 
echo "    \n"; 
echo "<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>\n"; 
echo "<script src=\"script.js\"></script>\n"; 
echo "</body>\n"; 
echo "</html>\n"; 
echo "\n";
?>