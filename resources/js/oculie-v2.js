class Oculie
{
	/*
	* Interface
	*/

	static createClass(label, code)
	{
		Oculie.register.saveClass(label, code);
	}

	static enableTimer(bool)
	{
		if(!Oculie.timer.status && !!bool)
		{
			Oculie.timer.status = true;
			Oculie.timer.timeout = 15;
			Oculie.timer.idx = 0;
			Oculie.timer.id = setInterval(Oculie.timer.seq, 16);
		}
		else if(Oculie.timer.status && !bool)
		{
			Oculie.timer.status = false;
			Oculie.timer.timeout = 0;
			Oculie.timer.idx = 0;
			clearInterval(Oculie.timer.id);
		}
	}

	static handle()
	{
		let o = null;
		switch(arguments.length)
		{
			case 0:
				o = document.getElementsByTagName("body")[0];
				break;
			case 1:
				if(typeof arguments[0]=="object"&&arguments[0] instanceof Event)
					o = document.getElementsByTagName("body")[0];
				else
					o = arguments[0];
				break;
			case 2:
				Oculie.throwError("in Oculie.bindObject : 2 arguments case NIY");
				break;
			default:
				Oculie.throwError("in Oculie.bindObject : n arguments case NIY");
				break;
		}

		return Oculie.handler.handle(o);
	}

	static start()
	{
		if(window.onload)
		{
			let loader = window.onload;
			window.onload = function(ev){
				loader(ev);
				Oculie.handle().implementAll();
			};
		}
		//else document.body.addEventListener("load", ()=>{Oculie.handle().implementAll()});

		else document.addEventListener('DOMContentLoaded', (event) => {Oculie.handle().implementAll()});
		/*else window.onload = function(ev)
		{
			Oculie.handle().implementAll();
		}
		*/
	}

	constructor(){}

	/*
	* Properties & Routines
	*/

	static handler = new class {
		handle(object)
		{
			this.object = object;
			return this;
		}

		implement()
		{
			if(!arguments.length)
			{
				let rc = Oculie.objectInquirer.inquire(this.object).getRegisteredClass();
				for(let i in rc)
				{
					if(!Oculie.objectInquirer.inquire(this.object).isImplementationOf(rc[i]))
					{
						Oculie.handler.handle(this.object).implement(rc[i]);
					}
				}
			}
			else
			{
				let cl = arguments[0];//TODO: tester isRegisteredClass(cl)
				if(!Oculie.objectInquirer.inquire(this.object).isRegistered())
				{
					Oculie.register.saveNode(this.object);
				}
				for(let i in Oculie.registered.graphicObject)
				{
					if(Oculie.registered.graphicObject[i].node===this.object)
					{
						if(!Oculie.objectInquirer.inquire(this.object).isImplementationOf(cl))
						{
							Oculie.registered.graphicObject[i].implementation[cl] = new Oculie.registered.class[cl](Oculie.registered.graphicObject[i].node);
						}
					}
				}
			}

			return this;
		};

		implementAll()
		{
			Oculie.handler.handle(this.object).implement();
			if(this.object.hasChildNodes())
			{
				for(let i=this.object.firstChild;i!=null;i=i.nextSibling)
				{
					if(i.nodeType!=1)continue;
					Oculie.handler.handle(i).implementAll();
				}
			}
		}
	}();

	static objectInquirer = new class {
		inquire(object)
		{
			this.object = object;
			return this;
		}

		getRegisteredClass()
		{
			let rc = [];
			if(this.object.hasAttribute("class"))
			{
				let oc = this.object.getAttribute("class").split(" ");
				for(let i in oc)if(!!Oculie.registered.class[oc[i]])rc.push(oc[i]);
			}
			return rc;
		}

		isImplementationOf(rc)
		{
			let r = false;
			for(let i in Oculie.registered.graphicObject)
			{
				if(Oculie.registered.graphicObject[i].node===this.object && !!Oculie.registered.graphicObject[i].implementation[rc])
				{
					r = true; break;
				}
			}
			return r;
		}

		isRegistered()
		{
			let r = false;
			for(let i in Oculie.register.graphicObject)
			{
				if(Oculie.registered.graphicObject[i].node===this.object)
				{
					r = true; break;
				}
			}
			return r;
		}
	}();

	static register = new class {
		saveNode(node)
		{
			if(!Oculie.objectInquirer.inquire(node).isRegistered())
			{
				Oculie.registered.graphicObject.push({
					node: node,
					implementation: {}
				});
			}
		}

		saveClass(label, code)
		{
			Oculie.registered.class[label] = code;
		}
	}();

	static debug = new class{
		throwError(errMsg)
		{
			alert(errMsg);
		}
	}();

	static timer = {
		id: null,
		idx: 0,
		status: false,
		timeout: null,
		instance: [],
		seq: function(){
			if(!Oculie.timer.idx)
			{
				for(let i in Oculie.timer.instance)
				{
					Oculie.timer.instance[i]();
				}
			}
			else
			{
				let resetIdx = true;
				for(let i=Oculie.timer.idx;i<Oculie.timer.instance.length;i++){}
				for(let i=0;i<Oculie.timer.idx;i++){}
				if(resetIdx)Oculie.timer.idx = 0;//TODO: resetIdx = false si interruption timeout (bouclage des instances imcomplet)
			}
		}
	};

	static registered =
	{
		"class": {},
		"graphicObject": []
	}
};

let oclNdHInterface = {
	select: function(p)
	{
		let r = null;
		oclPrv.txp = oclPrv.nxp+p;
		let xpv = document.evaluate(oclPrv.txp, document, null, XPathResult.ANY_TYPE, null);
		//alert("result type : "+xpv.resultType);
		switch(xpv.resultType)
		{
			case xpv.ANY_TYPE:
				break;
			case xpv.NUMBER_TYPE:
				break;
			case xpv.STRING_TYPE:
				break;
			case xpv.BOOLEAN_TYPE:
				break;
			case xpv.UNORDERED_NODE_ITERATOR_TYPE:
				r = [];
				let i; while(i=xpv.iterateNext()) r.push(i);
				oclPrv.rType = 4;
				oclPrv.r = r;
				break;
			case xpv.ORDERED_NODE_ITERATOR_TYPE:
				break;
			case xpv.UNORDERED_NODE_SNAPSHOT_TYPE:
				break;
			case xpv.ORDERED_NODE_SNAPSHOT_TYPE:
				break;
			case xpv.ANY_UNORDERED_NODE_TYPE:
				break;
			case xpv.FIRST_ORDERED_NODE_TYPE:
				break;
		}
		//oclPrv.tnd = document.evaluate(oclPrv.txp, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);
		return oclNdHInterface;
	},
	getNode: function()
	{
		let r = null;
		switch(oclPrv.rType)
		{
			case 1://int
			case 2://string
			case 3://node
				break;
			case 4://array
				let idx = arguments.length ? arguments[0] : 0;
				r = idx ? oclPrv.r[idx-1] : oclPrv.r;
				break;
		}
		oclPrv.rType = null;
		oclPrv.r = null;
		return r;
	},
	getNumber: function()
	{
		let r = null;
		switch(oclPrv.rType)
		{
			case 1://int
			case 2://string
			case 3://node
				break;
			case 4://array
				r = oclPrv.r.length;
				break;
		}
		return r;
	}
};

let oclTrgInterface = {
	execute: function()
	{
		let idx;
		idx = Oculie.timer.instance.push(arguments[0]);
		return idx;
	}
};

let oclPrv = {
	nd:null,
	nxp:null,
	txp:null,
	r:null,
	rType:null,
	getXPath: function()
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

function OclNodeHandler(node)
{
	oclPrv.nd = node;
	oclPrv.nxp = oclPrv.getXPath(oclPrv.nd);
	return oclNdHInterface;
}

class OculieUiClass
{
	constructor()
	{
		this.handle = OclNodeHandler;
		this.onEachFrame = oclTrgInterface;//TODO: verifier status timerEnable
	}
}

/**********************************************************************************************************************/


var $ocl = function()
{
	this.start = function() {};//Oculie.start()
}();

var OculieXmlHandler = function(dom)
{
	var dom = dom;
	var tdom;
	var xpath;

	this.getNode = function() {}
	this.select = function(p) {};
	var self = {"getDomXpath": function() {}};
};

var OculieApplication = function()
{
	this.dump = function() {};//TODO: implementer des fonction log et debug dans Oculie.debug
	this.checkDomObjectClassBond = function(dob, cn) {};//Oculie.inquirer.isImplementationOf()
	this.getRegisteredClass = function() {};//Oculie.inquirer.getRegisteredClass()
	this.init = function(){};//Oculie.handler.implementAll()
	this.register = function(cn, cb) {};//Oculie.createClass(label, code)

	this.useExternalLibs = function()//TODO: inserter des lib externe au niveau des constructeur de classe
	{
		externalLib = arguments[0];
	}
};
