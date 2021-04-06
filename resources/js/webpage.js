var WebPage = function(){
	this.init			= function()
	{
		var o = arguments[0] ? arguments[0] : document.getElementsByTagName("body")[0];
		for(var i=o;i!=null;i=i.nextSibling)
		{
			if(i.nodeType!=1)continue;
			var cl = system.getComponentClass(i);
			for(var j in cl){
				system.newInstance(cl[j],i);
			}
			if(i.hasChildNodes())self.init(i.firstChild);
		}
	};
	
	this.setClass 		= function()
	{
		system.newClass(arguments[0].name, arguments[0].value);
	};
	
	this.getClass 		= function(){};
	
	this.setConfig		= function(){
		if(!arguments[1])system.config=arguments[0];
		else system.config[arguments[1]]=arguments[0];
	};
	
	this.getConfig		= function(){
		if(!arguments[0]||!system.config[arguments[0]])return system.config;
		else return system.config[arguments[0]];
	};
	
	this.setEnv			= function(){
		system.env[arguments[0]]=arguments[1];
	};
	
	this.getEnv			= function(){
		return system.env[arguments[0]];
	};
	

	this.setEvent		= function(){
		system.event[arguments[0]]=arguments[1];
	};
	
	this.getEvent		= function(){};
	
	this.onEvent 		= function(){
		var e=arguments[0];
		return {execute: function(){system.event[e](arguments[0]);}};
	};

	
	this.setInstance 	= function(){
		system.env.ins[arguments[0].id] = arguments[0];
	};
	
	this.getInstance 	= function(){
		return system.env.ins[arguments[0]];
	};
	
	this.setMethod		= function(){
		if(this[arguments[0]])return;
		this[arguments[0]]=arguments[1];
	};
	
	this.getMethod		= function(){
	};
	
	this.setTask	= function()
	{
		system.newProc(arguments[0].name, arguments[0].data);
	};
	
	this.getProcessus	= function(){};
	
	this.onResponse = function()
	{
		var code = arguments[0] ? arguments[0] : "200";
		return {
			execute: function()
			{
				env.callback["HttpResponse"] = arguments[0];
			}
		};
	};
	
	this.open = function(url)
	{
		return {
			get:function()
			{
				system.urlGet(url);
			},
			
			post: function()
			{
				system.urlPost(url, arguments[0]);
			}
		};
	};
	
	this.execute 		= function(){};
	
	var self = this;
	var env = {
		timer: 		0,
		interval: 	20,
		mouse:		{
			x: null,
			y: null,
			isPress: 0
		},
		proc:		{},
		cls:		{},
		obj:		{},
		callback: 	{}
	};
	
	var system = {
		arg:		null,
		config:		{},
		env:		{
			nbi:	0,
			cls:	{},
			ins:	{}
		},
		event:		{},
	
		call: function()
		{
			if(!env.callback["HttpResponse"])return;
			env.callback["HttpResponse"](arguments[1]);
		},
		init: function(){
			
			
			window.onmousedown = function()
			{
				env.mouse.isPress = 1;
			};
			
			window.onmouseup = function()
			{
				env.mouse.isPress = 0;
			};
			
			window.onload = function(){
				self.init();
				system.start(arguments[0]);
			};
		},
		newClass: function()
		{
			system.env.cls[arguments[0]] = arguments[1];
		},
		newProc: function()
		{
			env.proc[arguments[0]] = arguments[1];
		},
		start: function()
		{
			alert("demarrage du systeme...");
			for(var i in env.proc)env.proc[i]();
		},
		urlGet: function()
		{
			var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
			xhr.open("GET", arguments[0], false);
			xhr.send(null);
			if (xhr.readyState == 4) return xhr.responseText;
		},
		urlPost: function()
		{
			var data = arguments[1] ? arguments[1] : null;
			var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
			xhr.onreadystatechange = function() 
			{
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) system.call(xhr.status, xhr.responseText);
			};
			xhr.open("POST", arguments[0], true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send(data);
		}
	};
	
	//system.init(arguments[0]);
	system.init("tetee");
};

$client = new WebPage();

/*
$client.setTask({
	name:"settings",
	data:function(){
		var ns = {'svg': 'http://www.w3.org/2000/svg'};
		var cfg = {};
		var serverAddr = $client.getConfig("server").addr ? $client.getConfig("server").addr : document.location.href;
		cfg["svg"] = !!document.createElementNS && !!document.createElementNS(ns.svg, 'svg').createSVGRect;
		cfg["doc"] = document.body ? {w:document.documentElement.clientWidth, h:document.documentElement.clientHeight} : {w:window.innerWidth, h:window.innerHeight};//document.body a la place de documentElement si non xhtml
		$client.onResponse().execute(function()
		{
			var o = arguments[0];
			var r = JSON.parse(o);
		});
		$client.open(serverAddr).post("Action=init&cfg="+JSON.stringify(cfg));
	}
});
*/

$client.setTask({
	name:"timer",
	data:function(){
		var cfg = $client.getConfig("timer");
		$client.setEnv("timer", {proc:[]});
		$client.setEvent("timer", function(){$client.getEnv("timer").proc.push(arguments[0]);});
		window.setInterval(function(){for(var i in $client.getEnv("timer").proc)if(typeof($client.getEnv("timer").proc[i])=="function")$client.getEnv("timer").proc[i]({pid:i});}, (cfg.tick?cfg.tick:20));
		$client.setMethod("start", function(){
			$client.onEvent("timer").execute(arguments[0]);
		});
		$client.setMethod("kill", function(){
			for(var i=0;i<$client.getEnv("timer").proc.length;i++)
			{
				if(i<arguments[0])continue;
				if(i==arguments[0])$client.getEnv("timer").proc[i]=null;
				if(i>0)$client.getEnv("timer").proc[i-1]=$client.getEnv("timer").proc[i];
				if(i==($client.getEnv("timer").proc.length-1))$client.getEnv("timer").proc.pop();
			}
		});
	}
});

/*
$client.setTask({
	name:"moveComp",
	data:function(){
		if($client.getEnv("mouse").isPress //&& env.selectedObject.target//)
		{
			//env.selectedObject.target.style = "top: "+(env.mouse.posY+env.selectedObject.offsetY)+"px; left: "+(env.mouse.posX+env.selectedObject.offsetX)+"px";
		}
	}
});
*/

$client.setConfig({
	server:{
		addr: null
	},
	timer:{
		tick: 20
	}
});
