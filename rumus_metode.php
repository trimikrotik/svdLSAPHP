<?php
ini_set("display_errors","Off");
    ini_set("register_globals","On");
error_reporting(E_ALL ^ E_NOTICE);

use src\Sastrawi\Stemmer\StemmerFactory;
use src\Sastrawi\Stemmer\Stemmer;
use src\Sastrawi\Stemmer\cachedStemmer;

function perkalian_matriks($matriks_a, $matriks_b) {
	$hasil = array();
	for ($i=0; $i<sizeof($matriks_a); $i++) {
		for ($j=0; $j<sizeof($matriks_b[0]); $j++) {
			$temp = 0;
			for ($k=0; $k<sizeof($matriks_b); $k++) {
				$temp += $matriks_a[$i][$k] * $matriks_b[$k][$j];
			}
			$hasil[$i][$j] = $temp;
		}
	}
	return $hasil;
}


function cetak_matriks($matriks)
{
	echo "<table border='1' cellspacing='0' cellpadding='5'>";
	for ($i=0; $i<sizeof($matriks); $i++) {
		echo "<tr>";
		for ($j=0; $j<sizeof($matriks[$i]); $j++) {
			echo "<td>". round($matriks[$i][$j], 5) ."</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

//coba fungsi cossim
function cosinesimilarity($matriks_a,$matriks_b)
{
    $nilaicossim = array();
    for ($i=0; $i<sizeof($matriks_a); $i++) {
        for ($j=0; $j<sizeof($matriks_b[0]); $j++) {
            $temp = 0;
            
            for ($k=0; $k<sizeof($matriks_b); $k++) {
                $temp += array_sum(array($matriks_a[$i][$k] * $matriks_b[$k][$j])) / (sqrt($matriks_a[$i][$k] * $matriks_b[$k][$j]) * sqrt($matriks_a[$i][$k] * $matriks_b[$k][$j]));
                if($temp < 0)
                {
                    $temp *= -1;
                }
            }
            $nilaicossim[$i][$j] = $temp;
        }
    }
    return $nilaicossim;
}

function cetak_cossim($matriks)
{
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    for ($i=0; $i<sizeof($matriks); $i++) {
        echo "<tr>";
        for ($j=0; $j<sizeof($matriks[$i]); $j++) {
            echo "<td>". round($matriks[$i][$j], 5) ."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

include 'koneksi.php';

mysql_query("TRUNCATE termkeyword");

$query = $_POST['cari']; //mengambil string dari textarea
$stringteks = $query;

//tokenisasi
$konversi = strtolower($stringteks); //mengubah huruf besar menjadi kecil menghasilkan kalimat "pengukuran similaritas dokumen"
$jenistandabaca = array(',', '!', '?', '.', ':',';', '-');
$hapustandabaca = str_replace($jenistandabaca,'',$konversi);
$hapustandabaca = trim(preg_replace('/[^0-9a-z]+/i','', $konversi)); //menghilangkan semua tanda baca
$hapustandabaca = preg_replace('/[^a-z\d]+/i', '', $konversi);
$hapustandabaca = preg_replace('/[^\w]+/','',$konversi);
$hapustandabaca = preg_replace('/\W+/','',$konversi);
$tokenbiasa   = strtok($query, " "); //mentoken query input
$replacespasi = str_replace(" ", PHP_EOL, $konversi);
$konversistring = explode("/", $konversi); //konversi string ke array
$kentoken = explode(" ", $konversi);//konversi string ke array. array dari data input
$array = preg_split('/[\pZ\pC]+/u', $konversi);
$ubahkarakter = str_replace(" ", '<br/>', $konversi);
$string = $replacespasi;

#-----------------------------------proses penghapusan stopword------------------------------------#
$stopword = array('dan','serta','atau','melainkan','tetapi','padahal','sedangkan','yang','agar','supaya','biar','jika','kalau','asal','asalkan','bila','manakala','sejak','semenjak','sedari','sewaktu','tatkala','ketika','sementara','begitu','seraya','selagi','selama','serta','sambil','demi','setelah','sesudah',' sebelum','sehabis','selesai','seusai','hingga','sampai','andaikan','seandainya','umpamanya','sekiranya','biar','biarpun','walau','walaupun','sekalipun','sungguhpun','kendati','kendatipun','seakan-akan','seolah-olah','sebagaimana','seperti','sebagai','laksana','ibarat','daripada', 'alih-alih','sebab','karena','oleh','sehingga', 'sampai','maka','makanya','dengan','tanpa','dengan','tanpa','bahwa','sama','lebih','bagi','pada','sangat','hanya','lebih','beberapa','banyak','sedikit','kami','mereka','kita','itu', 'di','ke','dari','untuk','guna','hingga','hampir','demi','atas','di mana','yang mana','dan','atau','tetapi','ketika','seandainya','supaya','walaupun','seperti','oleh karena','sehingga','bahwa','sang','para','umat','akan', 'dan','serta','atau','melainkan','tetapi','padahal','sedangkan','agar','supaya','jika','kalau','asal','asalkan','bila','manakala','sejak','semenjak','sedari','sewaktu','tatkala','ketika','sementara','begitu','seraya','selagi','selama','serta','sambil','demi','setelah','sesudah',' sebelum','sehabis','selesai','hingga','sampai','andaikan','seandainya','umpamanya','sekiranya','biarpun','pun','sekalipun','sungguhpun','kendati','kendatipun','seakan-akan','seolah-olah','sebagaimana','seperti','sebagai','laksana','ibarat','daripada', 'alih-alih','sebab','karena','oleh','sehingga', 'sampai','maka','dengan','tanpa','dengan','tanpa','bahwa','sama','lebih','bagi','pada','sangat','hanya','lebih','beberapa','banyak','sedikit','kami','mereka','kita','itu', 'di','ke','dari','untuk','guna','hingga','hampir','demi','atas','di mana','atau','tetapi','ketika','seandainya','supaya','walaupun','seperti','oleh karena','sehingga','bahwa','sang','para','umat','ada','adalah','adanya','adapun','agak','agaknya','agar','akan','akankah','akhir','akhiri','akhirnya','aku','akulah','amat','amatlah','anda','andalah','antar','antara','antaranya','apa','apaan','apabila','apakah','apalagi','
apatah','artinya','asal','asalkan','atas','atau','ataukah','ataupun','awal','awalnya','bagai','bagaikan','bagaimana','bagaimanakah','bagaimanapun','bagi','bagian','bahkan','bahwa','bahwasanya','baik','bakal','bakalan','balik','banyak','bapak','baru','bawah','beberapa','begini','beginian','beginikah','beginilah','begitu','begitukah','begitulah','begitupun','bekerja','belakang','belakangan','belum','belumlah','benar','benarkah','benarlah','berada','berakhir','berakhirlah','berakhirnya','berapa','berapakah','berapalah','berapapun','berarti','berawal','berbagai','berdatangan','beri','berikan','berikut','berikutnya','berjumlah','berkali-kali','berkata','berkehendak','berkeinginan','berkenaan','berlainan','berlalu','berlangsung','berlebihan','bermacam','bermacam-macam','bermaksud','bermula','bersama','bersama-sama','bersiap','bersiap-siap','bertanya','bertanya-tanya','berturut','berturut-turut','bertutur','berujar','berupa','besar','betul','betulkah','biasa','biasanya','bila','bilakah','bisa','bisakah','boleh','bolehkah','bolehlah','buat','bukan','bukankah','bukanlah','bukannya','bulan','bung','cara','caranya','cukup','cukupkah','cukuplah','cuma','dahulu','dalam','dapat','dari','daripada','datang','dekat','demi','demikian','demikianlah','dengan','depan','dia','diakhiri','diakhirinya','dialah','diantara','diantaranya','diberi','diberikan','diberikannya','dibuat','dibuatnya','didapat','didatangkan','digunakan','diibaratkan','diibaratkannya','diingat','diingatkan','diinginkan','dijawab','dijelaskan','dijelaskannya','dikarenakan','dikatakan','dikatakannya','dikerjakan','diketahui','diketahuinya','dikira','dilakukan','dilalui','dilihat','dimaksud','dimaksudkan','dimaksudkannya','dimaksudnya','diminta','dimintai','dimisalkan','dimulai','dimulailah','dimulainya','dimungkinkan','dini','dipastikan','diperbuat','diperbuatnya','dipergunakan','diperkirakan','diperlihatkan','
diperlukan','diperlukannya','dipersoalkan','dipertanyakan','dipunyai','diri','dirinya','disampaikan','disebut','disebutkan','disebutkannya','disini','disinilah','ditambahkan','ditandaskan','ditanya','ditanyai','ditanyakan','ditegaskan','ditujukan','ditunjuk','ditunjuki','ditunjukkan','ditunjukkannya','ditunjuknya','
dituturkan','dituturkannya','diucapkan','diucapkannya','diungkapkan','dong','dua','dulu','empat','enggak','enggaknya','entah','entahlah','guna','gunakan','hal','
hampir','hanya','hanyalah','hari','harus','haruslah','harusnya','hendak','hendaklah','hendaknya','hingga','ialah','ibarat','ibaratkan','ibaratnya','ibu','ikut','ingat','ingat-ingat','ingin','inginkah','inginkan','ini','inikah','inilah','itu','itukah','itulah','jadi','jadilah','jadinya','jangan','jangankan','janganlah','jauh','jawab','jawaban','jawabnya','jelas','jelaskan','jelaslah','jelasnya','jika','jikalau','juga','jumlah','jumlahnya','justru','kala','kalau','kalaulah','kalaupun','kalian','kami','kamilah','kamu','kamulah','kapan','kapankah','kapanpun','karena','karenanya','kasus','kata','katakan','katakanlah','katanya','keadaan','kebetulan','kecil','kedua','keduanya','keinginan','kelamaan','kelihatan','kelihatannya','kelima','keluar','kembali','kemudian','kemungkinan','kemungkinannya','kenapa','kepada','kepadanya','kesampaian','keseluruhan','keseluruhannya','keterlaluan','ketika','khususnya','kini','kinilah','kira','kira-kira','kiranya','kita','kitalah','kurang','lagi','lagian','lah','lain','lainnya','lalu','lama','lamanya','lanjut','lanjutnya','lebih','lewat','lima','luar','macam','maka','makanya','makin','malah','malahan','mampu','mampukah','mana','manakala','manalagi','masa','masalah','masalahnya','masih','masihkah','masing','masing-masing','mau','maupun','melainkan','melakukan','melalui','melihat','melihatnya','memang','memastikan','memberi','memberikan','membuat','memerlukan','memihak','meminta','memintakan','memisalkan','memperbuat','mempergunakan','memperkirakan','memperlihatkan','mempersiapkan','mempersoalkan','mempertanyakan','mempunyai','memulai','memungkinkan','menaiki','menambahkan','menandaskan','menanti','menanti-nanti','menantikan','menanya','
menanyai','menanyakan','mendapat','mendapatkan','mendatang','mendatangi','mendatangkan','menegaskan','mengakhiri','mengapa','mengatakan','mengatakannya','mengenai','mengerjakan','mengetahui','menggunakan','menghendaki','mengibaratkan','mengibaratkannya','mengingat','mengingatkan','menginginkan','mengira','mengucapkan','mengucapkannya','mengungkapkan','menjadi','menjawab','menjelaskan','menuju','menunjuk','menunjuki','menunjukkan','menunjuknya','menurut','menuturkan','menyampaikan','menyangkut','menyatakan','menyebutkan','menyeluruh','menyiapkan','merasa','mereka','merekalah','merupakan','meski','meskipun','meyakini','meyakinkan','minta','mirip','misal','misalkan','misalnya','mula','mulai','mulailah','mulanya','mungkin','mungkinkah','nah','naik','namun','nanti','nantinya','nyaris','nyatanya','oleh','olehnya','pada','padahal','padanya','pak','paling','panjang','pantas','para','pasti','pastilah','penting','pentingnya','percuma','perlu','perlukah','perlunya','pernah','persoalan','pertama','pertama-tama','pertanyaan','pertanyakan','pihak','pihaknya','pukul','pula','pun','punya','rasa','rasanya','rata','rupanya','saat','saatnya','saja','sajalah','saling','sama','sama-sama','sambil','sampai','sampai-sampai','sampaikan','sana','sangat','sangatlah','satu','saya','sayalah','sebab','sebabnya','sebagai','sebagaimana','sebagainya','sebagian','sebaik','sebaik-baiknya','sebaiknya','sebaliknya','sebanyak','sebegini','sebegitu','sebelum','sebelumnya','sebenarnya','seberapa','sebesar','sebetulnya','sebisanya','sebuah','sebut','sebutlah','sebutnya','secara','secukupnya','sedang','sedangkan','sedemikian','sedikit','sedikitnya','seenaknya','segala','segalanya','segera','seharusnya','sehingga','seingat','sejak','sejauh','sejenak','sejumlah','sekadar','sekadarnya','sekali','sekali-kali','sekalian','sekaligus','sekalipun','sekarang','sekecil','seketika','sekiranya','sekitar','sekitarnya','sekurang-kurangnya','sekurangnya','sela','selain','selaku','selalu','selama','selama-lamanya','selamanya','selanjutnya','seluruh','seluruhnya','semacam','semakin','semampu','semampunya','semasa','semasih','semata','semata-mata','semaunya','sementara','semisal','semisalnya','sempat','semua','semuanya','semula','sendiri','sendirian','sendirinya','seolah','seolah-olah','seorang','sepanjang','sepantasnya','sepantasnyalah','seperlunya','seperti','sepertinya','sepihak','sering','seringnya','serta','serupa','sesaat','sesama','sesampai','sesegera','sesekali','seseorang','sesuatu','sesuatunya','sesudah','sesudahnya','setelah','setempat','setengah','seterusnya','setiap','setiba','setibanya','setidak-tidaknya','setidaknya','setinggi','sewaktu','siap','siapa','siapakah','siapapun','sini','sinilah','soal','soalnya','suatu','sudah','sudahkah','sudahlah','supaya','tadi','tadinya','tahu','tahun','tak','tambah','tambahnya','tampak','tampaknya','tandas','tandasnya','tanpa','tanya','tanyakan','tanyanya','tapi','tegas','tegasnya','telah','tempat','tengah','tentang','tentu','tentulah','tentunya','tepat','terakhir','terasa','terbanyak','terdahulu','terdapat','terdiri','terhadap','terhadapnya','teringat','teringat-ingat','terjadi','terjadilah','terjadinya','terkira','terlalu','terlebih','terlihat','termasuk','ternyata','tersampaikan','tersebut','tersebutlah','tertentu','tertuju','terus','terutama','tetap','tetapi','tiap','tiba','tiba-tiba','tidak','tidakkah','tidaklah','tiga','tinggi','tunjuk','turut','tutur','tuturnya','ucap','ucapnya','ujar','ujarnya','umum','umumnya','ungkap','ungkapnya','untuk','usah','usai','waduh','wahai','waktu','waktunya','walau','walaupun','wong','yaitu','yakin','yakni');

$hapus_stopword = str_ireplace($stopword,"", $string); //kata yang mengandung stopword dari kata kunci dihapus. ini isinya khusus untuk menghapus daftar stopword
#---------------------------------------------batas akhir stopword removal-----------------------------#
#-------------------------------permulaan stemming----------------------------------#
//stemming
#stemming confix stripping stemmer
spl_autoload_register(function($StemmerFactory)
{
    include $StemmerFactory . '.php';
    
});
@include 'src/Sastrawi/Stemmer/StemmerFactory.php';
@include 'src/Sastrawi/Stemmer/Stemm.php';
require_once __DIR__ . '/vendor/autoload.php';

           //fungsi stemming (codingan untuk proses stemming)
            $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();//ini adalah objek
            $stemmerFactory->createStemmer($isDev = false);
            $stemmerFactory->createDefaultDictionary($isDev = false);
           
            $stemmer  = $stemmerFactory->createStemmer();
            //stem
            $kalimat = $_POST['cari'];
            $output   = $stemmer->stem($kalimat); //fungsi untuk stemming
            
        
#---------------------------------- batas akhir codingan stem -------------------------------------------#

$hasilStopWordRemoval = array(); // isinya array(0). array masih kosong
$hasilStemming = array(); // isinya array(0), array masih kosong
$hasilfrekuensi = array();
global $hasilfrekuensi;
global $hasilStemming;
###-----------------------------------penulisan list keyword yg diinputkan user --------------------####
$tokenKe = 0;//variabel untuk menyimpan hasil token
//hilangkan stopword pada hasil token
foreach ($kentoken as $token) { //melooping variabel token sebagai hasil array. melooping sebanyak jumlah kata yang diinputkan. variabel $token berisi array
    $stopToken = str_ireplace($stopword,"", $token); //hapus semua stopword pada variabel token yang berisi data inputan

//PROSES STOPWORD & STEMMING pada inputan di textbox search engine ganoong
    if ($stopToken != "") {//jika masih ada stopword maka 
        $hasilStopWordRemoval[$tokenKe] = $stopToken; //hapus stopword pada kata ke-1,2,3,4,5, dst.
        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        $hasilStemming[$tokenKe] = $stemmer->stem($hasilStopWordRemoval[$tokenKe]);//lakukan stemming pada inputan
        $tokenKe++; //looping sebanyak jumlah kata yang di inputkan
    }
} 
//penanganan text area
$frequency = 1; //variabel untuk mendefinisikan frekuensi inputan pada tabel term (frequency kata pada kolom frequency) pada tabel term. 
if(isset($_POST['cari']))
{
    //untuk mentoken setiap kata pada saat inputan. 
    /*dilakukan tokenisasi 2x karena untuk antisipasi ketika setelah stemming teks menjadi bentuk kalimat, bukan potongan kata dasar*/
    foreach ($hasilStemming as $value) //$value berisi kata dasar hasil stemming
    {
        $tokentoken = strtok($value, " ");
        $tokentoken1 = str_replace(" ", PHP_EOL, $value);
        $Token2 = strtok($stringteks, " ");
        $TOKEN = str_replace(" ", PHP_EOL, $stringteks);
        $imbuhan = '~^W*('.implode("|", array_map("preg_quote", $stopword)).')\W+\b|\b\W+(?1)\W*$~i';
        $ditoken = str_replace(" ",PHP_EOL, $query);
        $ditokenlagi = strtok($ditoken, " ");
        $tokenagain = trim($query);
        $sql = "INSERT INTO termkeyword (queryuser,frequency) VALUES ('".$value."','".$frequency."')";
        //mysqli_query($conn,$sql);
        mysql_query($sql);     
    }
    $utkFilter = array();//nilai array masih kosong
    $utkFrekuensi = array();

    foreach ($hasilStemming as $stem)
        { //variabel $stem berisi string inputan
            $utkFilter[] = "'".$stem."'";// variabel $utkFilter berisi array kata kunci. $utkFilter[] digunakan untuk menampilkan string inputan dimana pengulangannya dilakukan sebanyak jumlah kata yang di inputkan. misalnya kalimat terdiri dari 4 suku kata maka loopingnya 4x. namun datanya masih berupa array      
        }
        foreach($hasilfrekuensi as $frekuensi_kata)
        {
            $utkFrekuensi[] = "'".$frekuensi_kata."'";
        }
    $inFrekuensi = implode(",",$utkFrekuensi);  
    $inFilter = implode(",", $utkFilter);//konversi array ke string , variabel untuk menangkap setiap inputan query variabel $inFilter isinya kata kunci inputan user, misalnya "aplikasi pengukuran similaritas". di sini array inputan sudah berubah menjadi string. 
   //untuk mendefinisikan variabel doc
/*-----------------progres tgl 24 agustus 2017----------------------------------------------------------*/
//proses untuk menghitung jumlah term dari tiap-tiap dokumen. jika kata kunci di temukan maka dijumlahkan terus, namun jika tidak maka dikasih nilai default 1 untuk kolom query.
    $vectorQueryA = array();
    foreach ($utkFilter as $queryTerm) {//variabel $utkFilter isinya kata kunci dari client
		if ( ! array_key_exists($queryTerm, $vectorQueryA)) {
			$vectorQueryA[$queryTerm] = 1;
		}
		else {
			$vectorQueryA[$queryTerm]++;
		}
	}
	
	$vectorQuery = array();
	foreach ($vectorQueryA as $freq) {
		$vectorQuery[][0] = $freq;
	}



/*-------------------------------batas editing syntak 24 agustus 2017----------------------------------*/    
    

    $numOfDocs = 100;
    $docIds = array();
    global $docIds;
    for ($i = 1; $i <= $numOfDocs; $i++) 
    { //lakukan penulisan doc hingga doc ke 100 doc1 s.d. doc100
        $docIds[] = 'doc'.$i;//jika variabel ini di hilangkan maka tidak akan muncul apa-apa. hanya muncul tulisan array (). jadi variabel $docIds digunakan untuk penamaan doc1 - doc100. 
    }
    //variabel $inFilter isinya string 
    $sqlGetFreq = "SELECT * FROM termfrequency WHERE kata_kunci IN ($inFilter) ORDER BY kata_kunci,simbol_judul";//tampilkan data dari tabel termfrequency yang kata_kunci nya terdaftar di variabel $inFilter. variabel $inFilter isinya adalah string inputan user
    //$freqResult = mysqli_query($conn,"SELECT * FROM termfrequency WHERE kata_kunci IN ($inFilter) ORDER BY kata_kunci,simbol_judul ASC") or die(mysqli_error());
    $freqResult = mysql_query("SELECT * FROM termfrequency WHERE kata_kunci IN ($inFilter) ORDER BY kata_kunci,simbol_judul ASC") or die(mysql_error());

    $tf = array();
    
        while($tfRow = mysql_fetch_array($freqResult,MYSQL_ASSOC))
    { 
             if ( ! array_key_exists($tfRow['kata_kunci'], $tf))
        {
           $tf[$tfRow['kata_kunci']] = array(); //array kata_kunci
          
        }
            
                $tf[$tfRow['kata_kunci']][$tfRow['simbol_judul']] = $tfRow['frekuensi'];
            
    }
    mysql_free_result($freqResult); 

    print_r($tf);

      //dibawah ini adalah proses untuk menampilkan hasil matriks yang isinya : kata kunci user, kolom doc ke berapa , frekuensinya di dalam dokumen     
    $matrix_value = array();//variabel untuk menyimpan data yg berupa array
    foreach ($hasilStemming as $stem) 
    { 
        foreach ($docIds as $docId)
        {
            if (array_key_exists($stem, $tf))
            {
                $matrix_value[$stem][$docId] = 0;  
                 if (array_key_exists($docId, $tf[$stem]))
                {
                   $matrix_value[$stem][$docId] = $tf[$stem][$docId];
                }
            }
            else 
            {         
                $matrix_value[$stem][$docId] = 0; 
            }
        }
    }
    echo '========';
    echo '<pre>';//tag untuk memberikan batas spasi atas dan bawah antar nilai array
    print_r($matrix_value);
    echo '</pre>'; //tag penutup

    echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    foreach ($matrix_value as $term => $docFreq) {
        echo '<tr>';
        echo '<th>'.$term.'</th>';
        foreach ($docFreq as $docId => $freq) {
            echo '<td>'.intval($freq).'</td>';
            global $freq;

        }
        echo '</tr>';
    } 
    echo '</table>';

    include_once 'proses_svd.php'; 


/*----------------------------modifikasi progres ke dosen 24 agustus 2017-------------------------------*/
    /*bikin tabel untuk meletakkan matriks V transpose*/
    $tranposeV = ($matrixClass->matrixRound($USV['V']));
    echo "tabel matrix singular kanan";
    echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    foreach ($tranposeV as $nilaiV => $vectorVt)
    {
        echo '<tr>';
        echo '<th>'.$nilaiV.'</th>';
        foreach ($vectorVt as $nilaiV => $Vtranpose) {
            echo '<td>'.floatval($Vtranpose).'</td>';
            global $Vtranpose;
            
        }
        echo '</tr>';
    } 
    echo '</table>';
    /*batas modifikasi bikin tabel untuk V tranpose vector singular kanan*/
/*--------------------batas modifikasi progres ke dosen 24 agustus 2017-------------------------------*/
     
$matrix_value = array();//variabel untuk menyimpan data yg berupa array
    foreach ($hasilStemming as $stem) //looping kata kunci
    { // variabel hasilstemming digunakan untuk menyimpan data hasil stemming
        foreach ($docIds as $docId)//untuk melooping nama doc1 s.d. doc100
        {
            if (array_key_exists($stem, $tf))//untuk ngecek apakah kata kunci & frekuensi ada di database
            {   
                    
                            if($tf == 0)
                            {
                                 empty([$stem][$docId]);
                                 
                            }
                            else
                            {
                                $matrix_value[$stem][$docId] = $tf[$stem][$docId];
                            } 
                                if (array_key_exists($docId, $tf[$stem])) //cek doc & frekuensi term
                                {
                                    if($tf[$stem]==0)
                                    {
                                        empty($matrix_value[$stem][$docId]);
                                        empty($tf[$stem][$docId]);
                                        $matrix_value[$stem][$docId] = empty($tf[$stem][$docId]);   
                                    }
                                    else
                                    {
                                        $matrix_value[$stem][$docId] = $tf[$stem][$docId];
                                    }
                                                        
                                }
                                else
                                {
                                echo '';                       
                                }   
            }
            else 
            {         
                $matrix_value[$stem][$docId] = empty($matrix_value[$stem][$docId]);
                empty($matrix_value[$stem][$docId]); 
            }
        }
    }
    echo '======================*******************=======================';
    echo "<br />";
    echo "data dari dokumen yang terdapat kata kunci yang di inputkan user";
    echo '<pre>';//tag untuk memberikan batas spasi atas dan bawah antar nilai array
    print_r($matrix_value);
    echo '</pre>'; //tag penutup
     /*batas modifikasi*/  
}
else 
{ 
    //jika tidak maka tampilkan hasil dari proses di tabel term , perbarui kondisi tabel, hapus salah satu kata jika ada yang dobel
    //$tampil = mysqli_query($conn,"SELECT * FROM termkeyword") or die(mysqli_connect_error());
    $tampil = mysql_query("SELECT * FROM termkeyword") or die(mysql_connect_error());
    //while ($dokumen = mysqli_fetch_array($tampil,MYSQLI_ASSOC))
    while ($dokumen = mysql_fetch_array($tampil,MYSQL_ASSOC))
    {
     
        mysql_query("UPDATE termkeyword SET query='$output' ");
        $hapus_duplicate_query = implode(' ', array_unique(explode(' ', $output)));
     
        $imbuhan = '~^W*('.implode("|", array_map("preg_quote", $stopword)).')\W+\b|\b\W+(?1)\W*$~i';
        $ditoken = str_replace(" ",PHP_EOL, $query);
        $ditokenlagi = strtok($ditoken, " ");
    }
}

echo '======================*******************=======================';
    echo "<br />";
    echo "Vector Query";
    echo '<pre>';//tag untuk memberikan batas spasi atas dan bawah antar nilai array
    print_r($vectorQuery);
    echo '</pre>'; //tag penutup

    echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    foreach ($vectorQuery as $term => $vector_q)
    {
        echo '<tr>';
        echo '<th>'.$term.'</th>';
        foreach ($vector_q as $term => $vectorQueryfreq) {
            echo '<td>'.intval($vectorQueryfreq).'</td>';
            
        }
        echo '</tr>';
    } 
    echo '</table>';




echo "isi matrix Input USV";
//print_r($input_matrixUSV);
echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    foreach ($input_matrixUSV as $matrixUSV => $vectorUSV)
    {
        echo '<tr>';
        echo '<th>'.$matrixUSV.'</th>';
        foreach ($vectorUSV as $matrixUSV => $vectormatrixUSV) {
            echo '<td>'.intval($vectormatrixUSV).'</td>';
            global $vectormatrixUSV;
            
        }
        echo '</tr>';
    } 
    echo '</table>';
///////cari nilai vector document///////////////////////////////
$vectorDocumentB = array();
foreach ($input_matrixUSV as $documentTerm) {//variabel $utkFilter isinya kata kunci dari client
        if ( ! array_key_exists($documentTerm, $vectorQueryA)) {
            $vectorDocumentB[$documentTerm] = $vectormatrixUSV;
        }
        else {
            $vectorDocumentB[$documentTerm]++;
        }
    }
    
    $vectorDocument = array();
    foreach ($vectorDocumentB as $vectormatrixUSV) {
        $vectorDocument[][0] = $vectormatrixUSV;
    }
//////////////////////////////////////////////////////////////////////////////////////////







echo "isi matrix UkS";
print_r($UkS);
echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    foreach ($UkS as $matrixUkS => $vectorUkS)
    {
        echo '<tr>';
        echo '<th>'.$matrixUkS.'</th>';
        foreach ($vectorUkS as $matrixUkS => $vectormatrixUkS) {
            echo '<td>'.floatval($vectormatrixUkS).'</td>';
            $nilaiabsolut = abs($vectormatrixUkS);

            
        }
        echo '</tr>';
    } 
    echo '</table>';
    echo "nilai absolut vector UkS";
    echo "<br/><br/>";
    echo $nilaiabsolut;
    echo "<br>";
    echo "<br/><br/>";

    echo "variabel A : ";
    echo "<br/>";
    $vectorQA = array();
    $vectorQA[0] = $nilaiabsolut;
    $VectorUKS = array();
    $VectorUKS[0] = $vectormatrixUkS;
    $perkalianMatriks = array();
    $perkalianMatriks[0] = ($vectorQA[0]*$VectorUKS[0]);
    print_r($perkalianMatriks);
    global $perkalianMatriks;
   
    echo "<br/><br/>";
    echo "Vt x K";
    echo "<br/>";
    print_r($VtK);
    echo "<br/>";
    echo "tabel untuk VtK";
    echo "<br/><br/>";
echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    foreach ($VtK as $matrixVtK => $vectorVtK)
    {
        //echo '<tr>';
        echo '<th>'.$matrixVtK.'</th>';
        foreach ($vectorVtK as $matrixVtK => $vectormatrixVtk) {
          echo '<td>'.floatval($vectormatrixVtk).'</td>';            
        }
        echo '</tr>';
    } 
    echo '</table>';
    echo "<br/>";
    echo "VtK Transpose (Variabel B) :";
    echo "<br/>";
    print_r($VtkTranspose);
    echo "<br/><br/>";

    echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
    }
    echo '</tr>';
    echo '<tr>';
    foreach ($VtkTranspose as $matrixVtKTranspose => $vectorVtKTranspose)
    {
        echo '<tr>';
        echo '<th>'.$matrixVtKTranspose.'</th>';
        foreach ($vectorVtKTranspose as $matrixVtKTranspose => $vectormatrixVtkTranspose)
        {
            
            echo '<td>'.floatval($vectormatrixVtkTranspose).'</td>';
            global $vectorVtKTranspose;
            global $vectormatrixVtkTranspose;
                               
        }
        echo '</tr>';
        global $VtkTranspose;
    } 
    echo '<tr/>';
    echo '</table>';

//////////////////// docTerm_t * Uk * S (square) //////////////////////////////////

echo '//////////////////// docTerm_t * Uk * S (square) //////////////////////////////////';

// Loop utk tiap-tiap dokumen

echo '<h3>$vectorQuery (vektor baris)</h3>';
cetak_matriks($vectorQuery);
echo '<h3>$vectorQueryT (vektor kolom)</h3>';
$vectorQueryT = $matrixClass->matrixTranspose($vectorQuery);
cetak_matriks($vectorQueryT);

echo '<h3> $vectorDocument (vector barisnya vector Document)';
cetak_matriks($input_matrixUSV);
echo '<h3>$vectorDocumentT (vector kolomnya vector Document)';
$vectorDocumentT = $matrixClass->matrixTranspose($input_matrixUSV);
cetak_matriks($vectorDocumentT);

echo '<h3>$Uk</h3>';
cetak_matriks($Uk);
print_r($Uk);

echo '<h3>$UkT</h3>';
$UkT = $matrixClass->matrixTranspose($Uk);
cetak_matriks($UkT);
print_r($UkT);

echo '<h3>$S</h3>';
$S = $matrixClass->matrixRound($USV['S']);
cetak_matriks($S);
print_r($S);

// docTerm_t * Uk
echo '<h3>$vectorQuery_x_Uk</h3>';
$vectorQuery_x_Uk = perkalian_matriks($vectorQueryT, $Uk);
print_r($vectorQuery_x_Uk);

// freqTermdoc * Uk
echo '<h3>$vectorDocument_x_Uk</h3>';
$vectorDocument_x_Uk = perkalian_matriks($vectorDocumentT, $Uk);
print_r($vectorDocument_x_Uk);

echo "<br/>";
echo "Nilai vector baris dari Uk x Q adalah : ";
foreach ($Uk as $nilai_vector_baris_Uk)
{   
    cetak_matriks($vectorQuery_x_Uk);
    //print_r($vectorQuery_x_Uk);
}

echo "<br/>";
echo "Nilai vector baris dari Uk x d adalah";
foreach($Uk as $nilai_VectorBarisUk)
{
    cetak_matriks($vectorDocument_x_Uk);
}

// (docTerm_t * Uk) x S
echo "<br/>";
echo '<h3>Nilai q ($vectorQuery_x_Uk_x_$S). q untuk nilai vektor query </h3>';
$vectorQ_Uk_S = perkalian_matriks($vectorQuery_x_Uk, $S);
cetak_matriks($vectorQ_Uk_S);

//(freqTerm_T * Uk) * S
echo "<br/>";
echo '<h3> Nilai d ($vectorDocument_x_Uk_$S). d untuk nilai vector document </h3>';
$vectorD_Uk_S = perkalian_matriks($vectorDocument_x_Uk, $S);
cetak_matriks($vectorD_Uk_S);

//$vectorQ_Uk_S_transpose = ($matrixClass->matrixRound($vectorQ_Uk_S));
//cetak_matriks($vectorQ_Uk_S_transpose);
global $vectorQ_Uk_S;
global $vectorQ_Uk_S_transpose;



   echo '<h3>Nilai Cosine Similarity nya adalah :  </h3>';
      //$vector_singular_kanan = ($matrixClass->matrixRound($VtkTranspose));
   $cosine_similarity = cosinesimilarity($vectorQ_Uk_S, $vectorD_Uk_S);

  //$cosine_similarity = cosinesimilarity($vectorQ_Uk_S, $VtkTranspose);
   cetak_cossim($cosine_similarity);





   /*if($cosine_similarity < 0)
                {
                    $cosine_similarity *= -1;
                    global $cosine_similarity;
                    cetak_cossim($cosine_similarity);
                }*/
//$cosine_similarity = cosinesimilarity($vectorQ_Uk_S,$tranposeV);
   //$VtkTranspose
   //$vectormatrixVtkTranspose
   //$VtK
                
   
/*-------------------------coba fungsi cosine similarity dengan cara lain------------------------------*/
/*function dotproduct($q, $d){
     return array_sum(array_map(create_function('$a, $b', 'return $a * $b;'), $q, $d));
}
  $similarity=dotproduct($vectorQ_Uk_S,$VtkTranspose)/sqrt(dotproduct($vectorQ_Uk_S,$VtkTranspose)*dotproduct($vectorQ_Uk_S,$VtkTranspose));
echo $similarity; */
 /*------------------------batas rumus di atas--------------------------------------------------------*/
 
//penggunaan fungsi cosinesimilarity
//Matriks A atau q
//$vectorquery = array();
//$vectorquery[] = array($vectorQ_Uk_S);
/*$vectorquery[] = array(1, 2, 3);
$vectorquery[] = array(4, 5, 6);
$vectorquery[] = array(7, 8, 9);
$vectorquery[] = array(10, 11, 12);*/ 

//Matriks B atau d
//$vectordocument = array();
//$vectordocument[] = array($VtkTranspose);
/*$vectordocument = array(1, 2, 3, 4);
$vectordocument = array(5, 6, 7, 8);
$vectordocument = array(9, 10, 11, 12);*/
//$nilaicossim = cosinesimilarity($vectorquery,$vectordocument);

/*----------------------------------------------tabel cosine similariy----------------------------*/
/*echo "<table border='1' cellspacing='0' cellpadding='5'>";
for ($i=0; $i<sizeof($nilaicossim); $i++) {
    echo "<tr>";
    for ($j=0; $j<sizeof($nilaicossim[$i]); $j++) {
        echo "<td>". round($nilaicossim[$i][$j], 5) ."</td>";
    }
    echo "</tr>";
}
echo "</table>";*/
////////////////////////////////////////////////////////////////////////////////////////////////////
//printcos_sim($nilaicossim);

//$transpose_nilaicossim = ($matrixClass->matrixRound($nilaicossim));
    
     echo "Tabel Cosine Similarity :";
    /*Modifikasi Tabel CosSim*/
    echo '<table border="1">';
    echo '<tr>';
    echo '<th></th>';
    foreach ($docIds as $docId) {
        echo '<th>'.$docId.'</th>';
        global $docId;
    }
    echo '</tr>';
    echo '<tr>';
   
   foreach ($cosine_similarity as $vectorSudut => $sudutCosSim)
    //foreach ($result_cak_cosim as $vectorSudut => $sudutCosSim)
    {
        echo '<tr>';
        echo '<th>'.$vectorSudut.'</th>';
        foreach ($sudutCosSim as $vectorSudut => $vectorSudutCosSim)
        {
            //echo "<th>";
            foreach($docIds as $vectorSudutCosSim)

            //foreach($cosine_similarity as $vectorSudutCosSim)
          //  foreach($result_cak_cosim as $vectorSudutCosSim)
            {
                //echo "<th>";
                echo '<td>'.floatval($vectorSudutCosSim).'</td>';
                //echo '<td>'.floatval($cosine_similarity).'</td>';
               //echo "<th/>";
                global $cosine_similarity;
                global $vectorSudutCosSim;          
            }
            //echo "<th/>";           
                        
        }
        echo '</tr>';
    } 
    echo '<tr/>';
    echo '</table>';

   $hasilnilaicosinesimilarity = array();
   $hasilnilaicosinesimilarity[0] = $cosine_similarity;
      
foreach($hasilnilaicosinesimilarity as $item)
      //foreach($result_cak_cosim as $item)
{
    $parts = explode(",", $item);
   // mysqli_query( $conn,"TRUNCATE  cosinesimilarity");
    mysql_query("TRUNCATE  cosinesimilarity");
    $q = sprintf("INSERT INTO cosinesimilarity (cossim1, cossim2, cossim3,cossim4,cossim5,cossim6,cossim7,cossim8,cossim9,cossim10,cossim11, cossim12, cossim13,cossim14,cossim15,cossim16,cossim17,cossim18,cossim19,cossim20,cossim21, cossim22, cossim23,cossim24,cossim25,cossim26,cossim27,cossim28,cossim29,cossim30,cossim31, cossim32, cossim33,cossim34,cossim35,cossim36,cossim37,cossim38,cossim39,cossim40,cossim41, cossim42, cossim43,cossim44,cossim45,cossim46,cossim47,cossim48,cossim49,cossim50,cossim51, cossim52, cossim53,cossim54,cossim55,cossim56,cossim57,cossim58,cossim59,cossim60,cossim61, cossim62, cossim63,cossim64,cossim65,cossim66,cossim67,cossim68,cossim69,cossim70,cossim71, cossim72, cossim73,cossim74,cossim75,cossim76,cossim77,cossim78,cossim79,cossim80,cossim81, cossim82, cossim83,cossim84,cossim85,cossim86,cossim87,cossim88,cossim89,cossim90,cossim91, cossim92, cossim93,cossim94,cossim95,cossim96,cossim97,cossim98,cossim99,cossim100) VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s','%s', '%s', '%s')", $parts[0], $parts[1], $parts[2],$parts[3], $parts[4], $parts[5],$parts[6], $parts[7], $parts[8],$parts[9], $parts[10], $parts[11],$parts[12], $parts[13], $parts[14],$parts[15], $parts[16], $parts[17],$parts[18], $parts[19], $parts[20],$parts[21], $parts[22], $parts[23],$parts[24], $parts[25],$parts[26], $parts[27], $parts[28],$parts[29], $parts[30], $parts[31],$parts[32], $parts[33], $parts[34],$parts[35], $parts[36], $parts[37],$parts[38], $parts[39], $parts[40],$parts[41],$parts[42],$parts[43], $parts[44], $parts[45],$parts[46], $parts[47], $parts[48],$parts[49], $parts[50], $parts[51],$parts[52], $parts[53], $parts[54],$parts[55], $parts[56], $parts[57],$parts[58], $parts[59], $parts[60],$parts[61], $parts[62],$parts[63], $parts[64], $parts[65],$parts[66], $parts[67], $parts[68],$parts[69], $parts[70], $parts[71],$parts[72], $parts[73], $parts[74],$parts[75], $parts[76], $parts[77],$parts[78], $parts[79], $parts[80],$parts[81], $parts[82],$parts[83], $parts[84], $parts[85],$parts[86], $parts[87], $parts[88],$parts[89], $parts[90], $parts[91],$parts[92], $parts[93], $parts[94],$parts[95], $parts[96], $parts[97],$parts[98], $parts[99]);
    // now execute the query into your database - like
      mysql_query($q);
}
 
?>