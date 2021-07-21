(()=>{var t={7484:function(t){t.exports=function(){"use strict";var t=1e3,e=6e4,i=36e5,s="millisecond",n="second",r="minute",a="hour",o="day",u="week",h="month",d="quarter",l="year",c="date",f="Invalid Date",m=/^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[^0-9]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/,p=/\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,v={name:"en",weekdays:"Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),months:"January_February_March_April_May_June_July_August_September_October_November_December".split("_")},g=function(t,e,i){var s=String(t);return!s||s.length>=e?t:""+Array(e+1-s.length).join(i)+t},y={s:g,z:function(t){var e=-t.utcOffset(),i=Math.abs(e),s=Math.floor(i/60),n=i%60;return(e<=0?"+":"-")+g(s,2,"0")+":"+g(n,2,"0")},m:function t(e,i){if(e.date()<i.date())return-t(i,e);var s=12*(i.year()-e.year())+(i.month()-e.month()),n=e.clone().add(s,h),r=i-n<0,a=e.clone().add(s+(r?-1:1),h);return+(-(s+(i-n)/(r?n-a:a-n))||0)},a:function(t){return t<0?Math.ceil(t)||0:Math.floor(t)},p:function(t){return{M:h,y:l,w:u,d:o,D:c,h:a,m:r,s:n,ms:s,Q:d}[t]||String(t||"").toLowerCase().replace(/s$/,"")},u:function(t){return void 0===t}},w="en",M={};M[w]=v;var b=function(t){return t instanceof O},D=function(t,e,i){var s;if(!t)return w;if("string"==typeof t)M[t]&&(s=t),e&&(M[t]=e,s=t);else{var n=t.name;M[n]=t,s=n}return!i&&s&&(w=s),s||!i&&w},_=function(t,e){if(b(t))return t.clone();var i="object"==typeof e?e:{};return i.date=t,i.args=arguments,new O(i)},$=y;$.l=D,$.i=b,$.w=function(t,e){return _(t,{locale:e.$L,utc:e.$u,x:e.$x,$offset:e.$offset})};var O=function(){function v(t){this.$L=D(t.locale,null,!0),this.parse(t)}var g=v.prototype;return g.parse=function(t){this.$d=function(t){var e=t.date,i=t.utc;if(null===e)return new Date(NaN);if($.u(e))return new Date;if(e instanceof Date)return new Date(e);if("string"==typeof e&&!/Z$/i.test(e)){var s=e.match(m);if(s){var n=s[2]-1||0,r=(s[7]||"0").substring(0,3);return i?new Date(Date.UTC(s[1],n,s[3]||1,s[4]||0,s[5]||0,s[6]||0,r)):new Date(s[1],n,s[3]||1,s[4]||0,s[5]||0,s[6]||0,r)}}return new Date(e)}(t),this.$x=t.x||{},this.init()},g.init=function(){var t=this.$d;this.$y=t.getFullYear(),this.$M=t.getMonth(),this.$D=t.getDate(),this.$W=t.getDay(),this.$H=t.getHours(),this.$m=t.getMinutes(),this.$s=t.getSeconds(),this.$ms=t.getMilliseconds()},g.$utils=function(){return $},g.isValid=function(){return!(this.$d.toString()===f)},g.isSame=function(t,e){var i=_(t);return this.startOf(e)<=i&&i<=this.endOf(e)},g.isAfter=function(t,e){return _(t)<this.startOf(e)},g.isBefore=function(t,e){return this.endOf(e)<_(t)},g.$g=function(t,e,i){return $.u(t)?this[e]:this.set(i,t)},g.unix=function(){return Math.floor(this.valueOf()/1e3)},g.valueOf=function(){return this.$d.getTime()},g.startOf=function(t,e){var i=this,s=!!$.u(e)||e,d=$.p(t),f=function(t,e){var n=$.w(i.$u?Date.UTC(i.$y,e,t):new Date(i.$y,e,t),i);return s?n:n.endOf(o)},m=function(t,e){return $.w(i.toDate()[t].apply(i.toDate("s"),(s?[0,0,0,0]:[23,59,59,999]).slice(e)),i)},p=this.$W,v=this.$M,g=this.$D,y="set"+(this.$u?"UTC":"");switch(d){case l:return s?f(1,0):f(31,11);case h:return s?f(1,v):f(0,v+1);case u:var w=this.$locale().weekStart||0,M=(p<w?p+7:p)-w;return f(s?g-M:g+(6-M),v);case o:case c:return m(y+"Hours",0);case a:return m(y+"Minutes",1);case r:return m(y+"Seconds",2);case n:return m(y+"Milliseconds",3);default:return this.clone()}},g.endOf=function(t){return this.startOf(t,!1)},g.$set=function(t,e){var i,u=$.p(t),d="set"+(this.$u?"UTC":""),f=(i={},i[o]=d+"Date",i[c]=d+"Date",i[h]=d+"Month",i[l]=d+"FullYear",i[a]=d+"Hours",i[r]=d+"Minutes",i[n]=d+"Seconds",i[s]=d+"Milliseconds",i)[u],m=u===o?this.$D+(e-this.$W):e;if(u===h||u===l){var p=this.clone().set(c,1);p.$d[f](m),p.init(),this.$d=p.set(c,Math.min(this.$D,p.daysInMonth())).$d}else f&&this.$d[f](m);return this.init(),this},g.set=function(t,e){return this.clone().$set(t,e)},g.get=function(t){return this[$.p(t)]()},g.add=function(s,d){var c,f=this;s=Number(s);var m=$.p(d),p=function(t){var e=_(f);return $.w(e.date(e.date()+Math.round(t*s)),f)};if(m===h)return this.set(h,this.$M+s);if(m===l)return this.set(l,this.$y+s);if(m===o)return p(1);if(m===u)return p(7);var v=(c={},c[r]=e,c[a]=i,c[n]=t,c)[m]||1,g=this.$d.getTime()+s*v;return $.w(g,this)},g.subtract=function(t,e){return this.add(-1*t,e)},g.format=function(t){var e=this;if(!this.isValid())return f;var i=t||"YYYY-MM-DDTHH:mm:ssZ",s=$.z(this),n=this.$locale(),r=this.$H,a=this.$m,o=this.$M,u=n.weekdays,h=n.months,d=function(t,s,n,r){return t&&(t[s]||t(e,i))||n[s].substr(0,r)},l=function(t){return $.s(r%12||12,t,"0")},c=n.meridiem||function(t,e,i){var s=t<12?"AM":"PM";return i?s.toLowerCase():s},m={YY:String(this.$y).slice(-2),YYYY:this.$y,M:o+1,MM:$.s(o+1,2,"0"),MMM:d(n.monthsShort,o,h,3),MMMM:d(h,o),D:this.$D,DD:$.s(this.$D,2,"0"),d:String(this.$W),dd:d(n.weekdaysMin,this.$W,u,2),ddd:d(n.weekdaysShort,this.$W,u,3),dddd:u[this.$W],H:String(r),HH:$.s(r,2,"0"),h:l(1),hh:l(2),a:c(r,a,!0),A:c(r,a,!1),m:String(a),mm:$.s(a,2,"0"),s:String(this.$s),ss:$.s(this.$s,2,"0"),SSS:$.s(this.$ms,3,"0"),Z:s};return i.replace(p,(function(t,e){return e||m[t]||s.replace(":","")}))},g.utcOffset=function(){return 15*-Math.round(this.$d.getTimezoneOffset()/15)},g.diff=function(s,c,f){var m,p=$.p(c),v=_(s),g=(v.utcOffset()-this.utcOffset())*e,y=this-v,w=$.m(this,v);return w=(m={},m[l]=w/12,m[h]=w,m[d]=w/3,m[u]=(y-g)/6048e5,m[o]=(y-g)/864e5,m[a]=y/i,m[r]=y/e,m[n]=y/t,m)[p]||y,f?w:$.a(w)},g.daysInMonth=function(){return this.endOf(h).$D},g.$locale=function(){return M[this.$L]},g.locale=function(t,e){if(!t)return this.$L;var i=this.clone(),s=D(t,e,!0);return s&&(i.$L=s),i},g.clone=function(){return $.w(this.$d,this)},g.toDate=function(){return new Date(this.valueOf())},g.toJSON=function(){return this.isValid()?this.toISOString():null},g.toISOString=function(){return this.$d.toISOString()},g.toString=function(){return this.$d.toUTCString()},v}(),x=O.prototype;return _.prototype=x,[["$ms",s],["$s",n],["$m",r],["$H",a],["$W",o],["$M",h],["$y",l],["$D",c]].forEach((function(t){x[t[1]]=function(e){return this.$g(e,t[0],t[1])}})),_.extend=function(t,e){return t.$i||(t(e,O,_),t.$i=!0),_},_.locale=D,_.isDayjs=b,_.unix=function(t){return _(1e3*t)},_.en=M[w],_.Ls=M,_.p={},_}()},3864:function(t,e,i){t.exports=function(t){"use strict";function e(t){return t&&"object"==typeof t&&"default"in t?t:{default:t}}var i=e(t),s={name:"es-do",weekdays:"domingo_lunes_martes_miércoles_jueves_viernes_sábado".split("_"),weekdaysShort:"dom._lun._mar._mié._jue._vie._sáb.".split("_"),weekdaysMin:"do_lu_ma_mi_ju_vi_sá".split("_"),months:"enero_febrero_marzo_abril_mayo_junio_julio_agosto_septiembre_octubre_noviembre_diciembre".split("_"),monthsShort:"ene_feb_mar_abr_may_jun_jul_ago_sep_oct_nov_dic".split("_"),weekStart:1,relativeTime:{future:"en %s",past:"hace %s",s:"unos segundos",m:"un minuto",mm:"%d minutos",h:"una hora",hh:"%d horas",d:"un día",dd:"%d días",M:"un mes",MM:"%d meses",y:"un año",yy:"%d años"},ordinal:function(t){return t+"º"},formats:{LT:"h:mm A",LTS:"h:mm:ss A",L:"DD/MM/YYYY",LL:"D [de] MMMM [de] YYYY",LLL:"D [de] MMMM [de] YYYY h:mm A",LLLL:"dddd, D [de] MMMM [de] YYYY h:mm A"}};return i.default.locale(s,null,!0),s}(i(7484))},7412:function(t){t.exports=function(){"use strict";return function(t,e){e.prototype.isSameOrBefore=function(t,e){return this.isSame(t,e)||this.isBefore(t,e)}}}()},4110:function(t){t.exports=function(){"use strict";return function(t,e,i){t=t||{};var s=e.prototype,n={future:"in %s",past:"%s ago",s:"a few seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",M:"a month",MM:"%d months",y:"a year",yy:"%d years"};function r(t,e,i,n){return s.fromToBase(t,e,i,n)}i.en.relativeTime=n,s.fromToBase=function(e,s,r,a,o){for(var u,h,d,l=r.$locale().relativeTime||n,c=t.thresholds||[{l:"s",r:44,d:"second"},{l:"m",r:89},{l:"mm",r:44,d:"minute"},{l:"h",r:89},{l:"hh",r:21,d:"hour"},{l:"d",r:35},{l:"dd",r:25,d:"day"},{l:"M",r:45},{l:"MM",r:10,d:"month"},{l:"y",r:17},{l:"yy",d:"year"}],f=c.length,m=0;m<f;m+=1){var p=c[m];p.d&&(u=a?i(e).diff(r,p.d,!0):r.diff(e,p.d,!0));var v=(t.rounding||Math.round)(Math.abs(u));if(d=u>0,v<=p.r||!p.r){v<=1&&m>0&&(p=c[m-1]);var g=l[p.l];o&&(v=o(""+v)),h="string"==typeof g?g.replace("%d",v):g(v,s,p.l,d);break}}if(s)return h;var y=d?l.future:l.past;return"function"==typeof y?y(h):y.replace("%s",h)},s.to=function(t,e){return r(t,e,this,!0)},s.from=function(t,e){return r(t,e,this)};var a=function(t){return t.$u?i.utc():i()};s.toNow=function(t){return this.to(a(this),t)},s.fromNow=function(t){return this.from(a(this),t)}}}()}},e={};function i(s){var n=e[s];if(void 0!==n)return n.exports;var r=e[s]={exports:{}};return t[s].call(r.exports,r,r.exports,i),r.exports}(()=>{"use strict";const t=function(t){return{id:t.id,name:t.name,label:t.label,placeholder:t.placeholder,type:t.type?t.type:"text",min:t.min,max:t.max,step:t.step,required:t.required,value:t.value?t.value:null,default:t.value?t.value:null,hasError:!1,errorMessage:null,disabled:!1,reset:function(){this.value=this.default,this.hasError=!1,this.errorMessage=null},setError:function(t){this.hasError=!0,this.errorMessage=t},isOk:function(){this.hasError=!1,this.errorMessage=null}}};const e=function(){return{originalData:null,title:"Registrar Estanque",inputs:{},name:null,type:null,diameter:null,width:null,long:null,maxHeight:null,effectiveHeight:null,errorInDepth:!1,capacity:null,wire:void 0,dispatch:null,waiting:!1,register:!0,updating:!1,init:function(t,e){this.wire=t,this.dispatch=e,this.__buildInputs()},__buildInputs:function(){this.name=t({id:"fishpondName",name:"name",label:"Nombre",placeholder:"Escribe el nombre del estanque",required:!0}),this.type=t({id:"fishpondType",name:"type",label:"Tipo de estanque",value:"rectangular",required:!0}),this.diameter=t({id:"fishponddiameter",name:"diameter",label:'Diametro del Estanque <span class="text-xs">[m]<span>',placeholder:"Ingresa el diametro del estanque",type:"number",min:.01,max:999.99,step:.01}),this.capacity=t({id:"fishpondCapacity",name:"capacity",label:'Capacidad <span class="text-xs">[und]<span>',type:"number",placeholder:"Ingresa la capacidad del estanque",min:1,max:65535,step:1}),this.width=t({id:"fishpondWidth",name:"width",label:'Ancho <span class="text-xs">[m]<span>',type:"number",placeholder:"ej: 200.45",min:.01,max:999.99,step:.01}),this.long=t({id:"fishpondLong",name:"long",label:'Largo <span class="text-xs">[m]<span>',type:"number",placeholder:"ej: 23.4",min:.01,max:999.99,step:.01}),this.maxHeight=t({id:"fishpondMaxHeight",name:"maxHeight",label:'Maxima <span class="text-xs">[m]<span>',placeholder:"ej: 3.2",type:"number",min:.01,max:9.99,step:.01}),this.effectiveHeight=t({id:"fishpondEffectiveHeight",name:"effectiveHeight",label:'Efectiva <span class="text-xs">[m]<span>',placeholder:"ej: 3",type:"number",min:.01,max:9.99,step:.01}),this.inputs.name=this.name,this.inputs.capacity=this.capacity,this.inputs.type=this.type,this.inputs.diameter=this.diameter,this.inputs.width=this.width,this.inputs.long=this.long,this.inputs.maxHeight=this.maxHeight,this.inputs.effectiveHeight=this.effectiveHeight},submit:function(){this.register?this.storeFishpond():this.updating&&this.updateFishpond()},storeFishpond:function(){var t=this;if(this.validateRegister()){this.disabledInputs(),this.waiting=!0;var e=this.__buildData();this.wire.storeFishpond(e).then((function(e){e.isOk?(t.hidden(),t.enabledInputs(),t.dispatch("new-fishpond-registered",e.data)):t.notifyErrors(e.errors),t.waiting=!1}))}},updateFishpond:function(){var t=this;if(this.validateRegister()){this.disabledInputs(),this.waiting=!0;var e=this.__buildData();this.wire.updateFishpond(this.originalData.id,e).then((function(e){e.isOk?(t.hidden(),t.dispatch("fishpond-updated",e.data)):t.notifyErrors(e.errors),t.waiting=!1,t.enabledInputs()}))}},mountFishpond:function(t){this.resetInputs(),this.originalData=t,this.name.value=t.name,this.capacity.value=t.capacity,this.diameter.value=t.diameter,this.width.value=t.width,this.long.value=t.long,this.maxHeight.value=t.maxHeight,this.effectiveHeight.value=t.effectiveHeight,this.type.value=t.type,this.enabledUpdatingForm()},hidden:function(){this.resetInputs(),this.enabledRegisterForm(),this.errorInDepth=!1,this.dispatch("hidden-modal")},resetInputs:function(){for(var t in this.inputs){if(Object.hasOwnProperty.call(this.inputs,t))this.inputs[t].reset()}this.errorInDepth=!1,this.type.value="rectangular"},enabledRegisterForm:function(){this.register=!0,this.updating=!1,this.title="Registrar Estanque"},enabledUpdatingForm:function(){this.register=!1,this.updating=!0,this.title="Actualizar Registro"},disabledInputs:function(){for(var t in this.inputs){if(Object.hasOwnProperty.call(this.inputs,t))this.inputs[t].disabled=!0}},enabledInputs:function(){for(var t in this.inputs){if(Object.hasOwnProperty.call(this.inputs,t))this.inputs[t].disabled=!1}},__buildData:function(){var t=this.name.value,e=this.capacity.value?parseInt(this.capacity.value):null,i=this.type.value,s=null,n=null,r=null,a=this.maxHeight.value?parseFloat(this.maxHeight.value):null,o=this.effectiveHeight.value?parseFloat(this.effectiveHeight.value):null;return"circular"===i?s=this.diameter.value?parseFloat(this.diameter.value):null:"rectangular"===i&&(n=this.width.value?parseFloat(this.width.value):null,r=this.long.value?parseFloat(this.long.value):null),{name:t,capacity:e,type:i,diameter:s,width:n,long:r,max_height:a,effective_height:o}},validateInput:function(t){var e=this.inputs[t];"name"===t?this.validateName():"type"===t?this.validateType():this.validateNumber(e),"maxHeight"!==t&&"effectiveHeight"!==t||this.validateDepth()},validateName:function(){var t=this.name.value;t&&t.length>0?t.length<3?this.name.setError("Nombre demasiado corto"):t.length>20?this.name.setError("El nombre es demasiado largo"):this.name.isOk():this.name.setError("Este campo es requerido.")},validateType:function(){var t=this.type.value;"circular"===t||"rectangular"===t?this.type.isOk():this.type.setError("Tipo de estanque inválido")},validateNumber:function(t){var e=t.value;e?(e=parseFloat(t.value),isNaN(e)?t.setError("valor inválido"):void 0!==t.min&&void 0!==t.max?e>=t.min?e<=t.max?t.isOk():t.setError("Debe ser menor o igual que ".concat(t.max," m")):t.setError("Debe ser mayor o igual que ".concat(t.min,"  m")):void 0!==t.min?e>=t.min?t.isOk():t.setError("Debe ser mayor o igual que ".concat(t.min,"  m")):void 0!==t.max?e<=t.max?t.isOk():t.setError("Debe ser menor o igual que ".concat(t.max," m")):t.isOk()):t.isOk()},validateRegister:function(){var t=!0;for(var e in this.inputs)if(Object.hasOwnProperty.call(this.inputs,e)){var i=this.inputs[e];this.validateInput(e),i.hasError&&(t=!1)}return this.validateDepth(),t&&!this.errorInDepth},validateDepth:function(){var t=this.maxHeight,e=this.effectiveHeight,i=parseFloat(t.value),s=parseFloat(e.value);isNaN(i)||isNaN(s)||i>=s?(t.hasError&&t.errorMessage.length<=0&&t.isOk(),e.hasError&&e.errorMessage.length<=0&&e.isOk(),this.errorInDepth=!1):(t.hasError||t.setError(""),e.hasError||e.setError(""),this.errorInDepth=!0)},notifyErrors:function(t){for(var e in t)if(Object.hasOwnProperty.call(t,e)){var i=t[e];Object.hasOwnProperty.call(this.inputs,e)&&this.inputs[e].setError(i)}}}};const s=function(){return{title:"Registrar Costo",mode:"register",originalData:void 0,fishpond:void 0,costType:null,inThisMoment:!0,date:null,setTime:!1,time:null,description:null,amount:null,disabled:!1,waiting:!1,wire:void 0,dispatch:void 0,refs:void 0,init:function(t,e,i){this.wire=t,this.dispatch=e,this.refs=i,this.__buildInputs()},__buildInputs:function(){this.costType=t({id:"costType",name:"type",label:"Tipo de Costo",required:!0,value:""}),this.date=t({id:"costDate",name:"date",label:"Selecciona una fecha",required:!0,max:dayjs().format("YYYY-MM-DD")}),this.time=t({id:"costTime",name:"time",label:"Hora",required:!0}),this.description=t({id:"costDescription",name:"description",label:"Descripicíon",required:!0,placeholder:"Escribe una descripción del costo."}),this.amount=t({id:"costAmount",name:"amount",label:"Importe",required:!0,placeholder:"$ 0.00",max:1e8})},submit:function(){this.validateData()&&("register"===this.mode?this.store():"update"===this.mode&&this.update())},store:function(){var t=this,e=this.__buildData();this.waiting=!0,this.wire.storeFishpondCost(e).then((function(e){e.isOk?(t.hidden(),t.dispatch("new-fishpond-cost-registered",e.cost)):t.notifyErrors(e.errors),t.waiting=!1})).catch((function(t){console.log(t)}))},update:function(){var t=this,e=this.__buildData();e.costId=this.originalData.id,this.waiting=!0,this.wire.updateFishpondCost(e).then((function(e){e.isOk?(t.hidden(),t.dispatch("fishpond-cost-updated",e.cost)):t.notifyErrors(e.errors),t.waiting=!1})).catch((function(t){console.log(t)}))},mountCost:function(t){this.reset(),this.title="Actualizar Costo",this.costType.value=t.type,this.inThisMoment=!1,this.date.value=t.date,this.setTime=!0,this.time.value=t.time,this.description.value=t.description,this.amount.value=t.amount,this.originalData=t,this.refs.costAmount.value=window.formatCurrency(t.amount,0),this.mode="update"},reset:function(){this.costType.reset(),this.description.reset(),this.amount.reset(),this.refs.costAmount.value="",this.inThisMoment=!0,this.date.hasError&&this.date.reset(),this.time.hasError&&(this.setTime=!1,this.time.reset),this.title="Registrar Costo",this.mode="register"},hidden:function(){this.reset(),this.dispatch("hidden-modal")},__buildData:function(){var t=this.fishpond.id,e=this.costType.value,i=this.description.value,s=this.amount.value,n=this.inThisMoment,r=this.setTime;return{fishpondId:t,type:e,description:i,amount:s,inThisMoment:n,setTime:r,date:n?null:this.date.value,time:!n&&r?this.time.value:null}},formatAmount:function(t){var e=window.deleteCurrencyFormat(t.value);this.refs.costAmount.value=window.formatCurrency(e,0),this.amount.value=e,this.validateAmount()},validateData:function(){var t=[];return t.push(this.validateCostType(),this.validateDate(),this.validateTime(),this.validateDescription(),this.validateAmount()),!t.some((function(t){return!1===t}))},validateCostType:function(){var t=this.costType.value,e=!1;return"materials"===t||"workforce"===t||"maintenance"===t?(e=!0,this.costType.isOk()):""===t?this.costType.setError("Se debe seleccionar uno"):this.costType.setError("El tipo de costo seleccionado es incorrecto"),e},validateDate:function(){var t=this.date.value,e=!1;if(this.inThisMoment)e=!0;else if(t&&t.length>0){var i=window.dayjs(t);if(i.isValid()){var s=window.dayjs();i.isSameOrBefore(s)?(this.date.isOk(),this.validateTime(),e=!0):this.date.setError("La fecha superior a hoy")}else this.date.setError("La fecha es inválida")}else this.date.setError("Este campo es requerido");return e},validateTime:function(){var t=this.time.value,e=!1;if(this.setTime)if(t&&t.length>0){if(this.date.value&&this.date.value.length>0&&!this.date.hasError){var i=this.date.value,s=window.dayjs("".concat(i," ").concat(t)),n=window.dayjs();s.isValid()?(console.log(s.isSameOrBefore(n)),s.isSameOrBefore(n)?(this.time.isOk(),e=!0):this.time.setError("La combinacion fecha y hora superan al ahora")):this.time.setError("Formato de fecha inválido")}}else this.time.setError("El campo hora es requerido");else e=!0;return e},validateDescription:function(){var t=this.description.value,e=!1;return t&&t.length>0?t.length>=3?(this.description.isOk(),e=!0):this.description.setError("La descripción es muy pequeña"):this.description.setError("El campo descrioción es requerido"),e},validateAmount:function(){var t=this.amount.value,e=!1;if(t)if(t>0)if(t<this.amount.max)this.amount.isOk(),e=!0;else{var i=window.formatCurrency(this.amount.max,0);this.amount.setError("EL campo importe debe ser menor que ".concat(i))}else this.amount.setError("El importe debe ser mayor que cero (0)");else this.amount.setError("El campo importe es requerido.");return e},notifyErrors:function(t){for(var e in t)if(Object.hasOwnProperty.call(t,e)){var i=t[e];Object.hasOwnProperty.call(this,e)&&this[e].setError(i)}}}};var n=i(7484);i(3864);var r=i(4110);n.extend(r);var a=i(7412);n.extend(a),n.locale("es-do"),window.dayjs=n,window.registerForm=e,window.costForm=s,window.app=function(){return{fishponds:[],fishpondSelected:void 0,updatingModel:!1,showingModal:!1,showingRegisterModal:!1,showingCosts:!1,showingCostForm:!1,dispatch:void 0,wire:void 0,costType:{materials:"Costo de materiales",workforce:"Mano de obra",maintenance:"Costo de mantenimiento"},init:function(t,e){this.wire=t,this.dispatch=e,this.updateModel()},updateModel:function(){var t=this;this.updatingModel=!0,this.wire.getFishponds().then((function(e){e.forEach((function(e){e.costs.map((function(e){return t.formatCostDate(e),e})),t.sortFishpondCost(e),t.addNewFishpond(e)})),t.updatingModel=!1})).catch((function(e){console.log(e),t.updatingModel=!1}))},editFishpond:function(t){this.showRegisterForm(),this.dispatch("edit-fishpond",t)},editCost:function(t){this.showCostForm(),this.dispatch("edit-cost",t)},addNewFishpond:function(t){this.fishponds.push(t)},addNewFishpondCost:function(t){var e=this.fishponds.find((function(e){return e.id===t.fishpondId}));e&&(this.formatCostDate(t),e.costs.push(t),e.costsAmount+=t.amount,console.log(e),this.sortFishpondCost(e))},updateFishpond:function(t){var e=this,i=this.fishponds.find((function(e){return e.id===t.id}));if(i){for(var s in t)Object.hasOwnProperty.call(i,s)&&(i[s]=t[s]);i.costs.map((function(t){return e.formatCostDate(t),t}))}},updateFishpondCost:function(t){var e=this.fishponds.find((function(e){return e.id===t.fishpondId}));if(e){var i=e.costs.find((function(e){return e.id===t.id}));if(i){for(var s in e.costsAmount-=i.amount,this.formatCostDate(t),t)Object.hasOwnProperty.call(i,s)&&(i[s]=t[s]);console.log(i),e.costsAmount+=i.amount,this.sortFishpondCost(e)}}},destroyFishpond:function(t){var e=this;window.Swal.fire({title:"¿Desea eliminar este estanque?",text:"Esta acción no puede revertirse y eliminará toda la información del estanque junto con sus registros de costos.",icon:"warning",showCancelButton:!0,cancelButtonColor:"var(--primary)",confirmButtonColor:"var(--success)",confirmButtonText:"¡Eliminar!",showLoaderOnConfirm:!0,preConfirm:function(){return e.wire.destroyFishpond(t).then((function(t){return t}))},allowOutsideClick:function(){return!window.Swal.isLoadig()}}).then((function(i){if(i.isConfirmed)if(i.value.isOk){var s=e.fishponds.findIndex((function(e){return e.id===t}));s>=0&&e.fishponds.splice(s,1)}else i.result.errors.notFund&&location.reload()}))},destroyFishpondCost:function(t,e){var i=this;window.Swal.fire({title:"¿Desea eliminar este costo?",text:"Esta acción no puede revertirse ¿Está seguro que desea continuar?",icon:"warning",showCancelButton:!0,cancelButtonColor:"var(--primary)",confirmButtonColor:"var(--success)",confirmButtonText:"¡Si, Eliminar!",showLoaderOnConfirm:!0,preConfirm:function(){return i.wire.destroyFishpondCost(e,t).then((function(t){return t}))},allowOutsideClick:function(){return!window.Swal.isLoadig()}}).then((function(s){if(s.isConfirmed)if(s.value.isOk){var n=i.fishponds.find((function(t){return t.id===e}));if(n){var r=n.costs.findIndex((function(e){return e.id===t}));r>=0&&(n.costsAmount-=n.costs[r].amount,n.costs.splice(r,1))}}else s.result.errors.notFund&&location.reload()}))},showRegisterForm:function(){this.showingModal=!0,this.showingRegisterModal=!0},showCostForm:function(){this.showingModal=!0,this.showingCostForm=!0},showCosts:function(t){this.dispatch("fishpond-selected",t),this.fishpondSelected=t,this.showingCosts=!0},hiddenModal:function(){this.showingRegisterModal=!1,this.showingCostForm=!1,this.showingModal=!1},formatCostDate:function(t){t.fullDate="".concat(t.date," ").concat(t.time),t.dateFormat=n(t.date).format("dddd DD/MM/YY"),t.fromNow=n(t.fullDate).fromNow(),t.createdAt=n(t.createdAt).fromNow(),t.updatedAt=n(t.updatedAt).fromNow()},sortFishpondCost:function(t){t.costs.sort((function(t,e){var i=n(t.fullDate),s=n(e.fullDate);return i.isBefore(s)?-1:i.isSame(s)?0:1}))}}}})()})();
//# sourceMappingURL=app.js.map