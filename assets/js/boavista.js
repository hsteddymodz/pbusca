function PrintElem(elem)
{
    console.log('imprimindo');
    console.log(elem);
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');
    var html     = document.getElementById(elem).html();

    mywindow.document.write('<html><head><title>Imprimir Consulta: Score CPF - Probusca.com</title>');
    mywindow.document.write('</head><body>');
    mywindow.document.write('<h1>Imprimir Consulta: Score CPF - Probusca.com</h1>');
    //mywindow.document.write('<style>' + $('#mstyle').html() + '</style>' + html);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}