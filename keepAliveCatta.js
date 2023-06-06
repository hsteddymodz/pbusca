let https = require('https');
let req_number = 0;

async function doGet(url){
	return new Promise((resolve, reject) => {
		https.get(url, (res) => {
		  	const { statusCode } = res;
		  	const contentType = res.headers['content-type'];
		  	res.setEncoding('utf8');
		  	let rawData = '';
		 	res.on('data', (chunk) => { 
		 	 	rawData += chunk; 
		 	});
		  	res.on('end', () => {
		    	resolve(rawData);
		  	});
		}).on('error', (e) => {
			reject(e);
		});
	});
}
async function do_get(url) {
	req_number++;
	let curr_number = req_number;
	console.log('Request ('+ curr_number +') to: ' + url);
	doGet(url).then((e)=>{
		console.log('Response Success (' + curr_number + '): ', e);
	}).catch((e)=>{
		console.log('Response Error (' + curr_number + '): ', e);
	});
}

//do_get('https://probusca.com/painel/class/Catta.class.php?keepAlive=true');
//do_get('https://servidor.probusca.com:15000/assReset');
do_get('https://probusca.com/painel/class/localizacao.controller.php?doLogin=true');

setInterval(function() {
	do_get('https://probusca.com/painel/class/localizacao.controller.php?doLogin=true');
}, 30 * 60 * 1000);

/*
setInterval(function(){
	do_get('https://probusca.com/painel/class/Catta.class.php?keepAlive=true');
}, 15000);

setInterval(function(){
	do_get('https://servidor.probusca.com:15000/assReset');
}, 60 * 60 * 1000);
*/