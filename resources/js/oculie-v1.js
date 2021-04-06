var $ocl = function()
{
	var system = {
		started: false
	};

	this.start = function()
	{
		if(window.onload)
		{
			system.boot.push(window.onload);//TODO mettre dans un tableau si tableau de function
		}
		window.onload = function(){
			/***/
			 var dump = "";
			 dump = "Liste des classes : \n";
			 for(let i in instance.class) dump += i+"\n";alert(dump);
			 /***/
			system.init(/*arguments[0]*/);
		};
	};
}();

var OculieXmlHandler = function(dom)
{
	var dom = dom;
	var tdom;
	var xpath;

	this.getNode = function()
	{
		return tdom;
	}

	this.select = function(p)
	{
		let ctx=self.getDomXpath(dom);
		let r = "";
		let tmp = p.split("/");
		if(tmp.length>1)for(let i=2;i<tmp.length;i++)r=r+"/"+tmp[i];
		xpath = ctx+r;
		//let test = document.evaluate(xpath, document, null, XPathResult.ANY_TYPE, null);
		let test = document.evaluate(xpath, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);
		tdom = test.singleNodeValue;
		return this;
	};

	var self = {
		"getDomXpath": function()
		{
			let p = [];
			let dxp = "";
			for(let i=arguments[0]; i; i=i.parentNode)p.unshift(i);
			for(let i in p)
			{
				switch(p[i].nodeType)
				{
					case 1:
						let tmp = "";
						let tn=p[i].tagName; let k=0;
						for(let j=p[i];j;j=j.previousSibling)
						{
							if(j.nodeType!=1)continue;
							if(tn==j.tagName)k++;
						}
						dxp = dxp + "/"+tn+"["+k+"]";
						break;
					default://9
						break;
				}
			}
			return dxp;
		}
	};
};

var OculieApplication = function()
{
	//!constructor

	//!
	var self = {
		"register": {
			"class": {},
			"instance": []
		}
	};

	//!
	this.dump = function()
	{
		var dump = "objet enregistré :\n";
		for(let i in self["register"]["class"]) dump += i+"\n";alert(dump);
	};

	//!
	this.checkDomObjectClassBond = function(dob, cn)
	{
		let r = false;
		for(let i in self.instance)
		{
			if(self.instance[i].dob===dob && !!self.instance[i].class[cn])
			{
				r = true; break;
			}
		}
		return r;
	};

	this.getRegisteredClass = function()
	{
		var rc = [];
		if(!arguments.length)cl=self.register.class;
		else switch(arguments.length)
		{
			case 1:
				switch(typeof arguments[0])
				{
					case "object":
						if(arguments[0].hasAttribute("class"))
						{
							let oc = arguments[0].getAttribute("class").split(" ");
							for(let i in oc)if(!!self.register.class[oc[i]])rc.push(oc[i]);
						}
						break;
				}
				break;
		}
		return rc;
	};

	this.init = function()
	{
		var o = null;
		if(!arguments.length)o=document.getElementsByTagName("body")[0];
		else o=arguments[0];

		if(arguments.length>1)
		{
			let idx = null;
			let cn = arguments[1];

			for(let i in self.register.instance)if(self.register.instance[i].dob===o){idx=i;break;}
			if(idx === null)
			{
				idx = self.register.instance.length;
				self.register.instance[idx] = {"dob":o, "class":{}};
			}
			if(!self.register.instance[idx].class[cn])
			{
				self.register.instance[idx].class[cn] = new self.register.class[cn](o);
				self.register.instance[idx].class[cn].__construct(externalLib);
			}
		}
		else for(let i=o;i!=null;i=i.nextSibling)
		{
			if(i.nodeType!=1)continue;
			let cn = this.getRegisteredClass(i);
			for(var j in cn)
			{
				if(!this.checkDomObjectClassBond(o, cn[j])) this.init(o, cn[j]);
			}
			if(i.hasChildNodes())this.init(i.firstChild);
		}
	};

	this.register = function(cn, cb)
	{
		self.register.class[cn] = cb;
	};

	this.useExternalLibs = function()
	{
		externalLib = arguments[0];
	}
};
