"use strict";
const util = require('util');
const child_process = require('child_process');
const promisified_exec = util.promisify(child_process.exec);
const iplocation = require("iplocation").default;
let geoip = require('geoip-lite');

var stdin = process.openStdin();
let ip_list = {};
async function dropIp(ip, qtd){
	//await promisified_exec("iptables -A INPUT -s " + ip + " -j DROP");
	//console.log(ip + " DROPED");
	
	try {
		let location = geoip.lookup(ip);
		//console.log(location);
		console.log(qtd + ' - ' + ip + ' / ' + location.country);
		if(location.country != 'DE' && location.country != 'BR' && !ip_list[ip]){
			await promisified_exec("iptables -A INPUT -s " + ip + " -j DROP");
			console.log(ip + " DROPED from " + location.city + " - " + location.country);
			ip_list[ip] = true;
		}
		
	}catch(e){
		console.log("NOT DROPED " + ip, e);
	}
	
	
}

async function get_ip_array(){
	let ips = [];
	let cmd = "netstat -an | egrep ':80|:443' | grep ESTABLISHED | awk '{print $5}' | grep -o -E \"([0-9]{1,3}[\.]){3}[0-9]{1,3}\" | sort -n | uniq -c | sort -nr";
	let res = await promisified_exec(cmd);

	let lines = res.stdout.split('\n'), line;
	let need_restart = false;
	
	for(let l in lines){
		line = lines[l].trim().split(' ');
		if(line[0] && line[1])
			ips.push(line[1]);	
	}
	ips.sort();
	return ips;
}

async function start_protection(){

	let cmd = "netstat -an | egrep ':80|:443' | grep ESTABLISHED | awk '{print $5}' | grep -o -E \"([0-9]{1,3}[\.]){3}[0-9]{1,3}\" | sort -n | uniq -c | sort -nr";
	let res = await promisified_exec(cmd);

	let lines = res.stdout.split('\n'), line;
	let need_restart = false;
	
	for(let l in lines){
		line = lines[l].trim().split(' ');
		if(line[0] && line[1]){
//			console.log(line[0] + " - " + line[1]);
			dropIp(line[1], line[0]);
		}		
	}
	await promisified_exec("iptables-save");
}

if(process.argv[2] == '-t') {
	async function run(){
		let teste = await get_ip_array();
		for(let i in teste) {
			let tmp = teste[i].split('.');
			if(tmp[0] == process.argv[3]) {
				console.log(teste[i] + 'DROPED');
				await promisified_exec("iptables -A INPUT -s " + teste[i] + " -j DROP");
			}
		}
		//console.log(teste);
	}
	run();
} else {
	start_protection();
	setInterval(function(){
		start_protection();
	}, 60000);
}



