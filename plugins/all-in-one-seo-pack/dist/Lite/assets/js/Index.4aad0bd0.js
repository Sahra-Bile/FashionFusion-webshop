import{a as d}from"./Caret.11ded1aa.js";import{r as m,o as a,b as u,w as p,a as o,c as l,e as n,i as f,d as _,f as r,n as h,j as v}from"./vue.runtime.esm-bundler.0bc3eabf.js";/* empty css                                              */import{_ as y}from"./_plugin-vue_export-helper.8823f7c1.js";const w={emits:["close"],components:{SvgClose:d},props:{classes:{type:Array,default(){return[]}},noHeader:Boolean,isolate:Boolean,allowOverflow:Boolean,confirmation:Boolean},methods:{scrollToElement(){const e=this.$el.getElementsByClassName("component-wrapper")[0];setTimeout(()=>{e&&(e.firstChild.scrollTop=0)},10)},escapeListener(e){e.key==="Escape"&&!this.confirmation&&this.$emit("close")}},mounted(){document.addEventListener("keydown",this.escapeListener),this.scrollToElement(),this.isolate&&document.body.appendChild(this.$el)},beforeUnmount(){document.removeEventListener("click",this.escapeListener)}},C={class:"modal-mask"},$={class:"modal-wrapper"},k={class:"modal-container"},B={key:0,class:"modal-header"},g={class:"modal-body"},b={key:1,class:"modal-container__footer"};function E(e,s,t,L,T,A){const i=m("svg-close");return a(),u(v,{name:"modal"},{default:p(()=>[o("div",{class:h(["aioseo-modal",[{"aioseo-app":t.isolate,"allow-overflow":t.allowOverflow},...t.classes]])},[o("div",C,[o("div",$,[o("div",k,[t.noHeader?r("",!0):(a(),l("div",B,[n(e.$slots,"header",{},()=>[n(e.$slots,"headerTitle"),o("button",{class:"close",type:"button",onClick:s[1]||(s[1]=f(c=>e.$emit("close"),["stop"]))},[_(i,{onClick:s[0]||(s[0]=c=>e.$emit("close"))})])])])),o("div",g,[n(e.$slots,"body")]),e.$slots.footer?(a(),l("div",b,[n(e.$slots,"footer")])):r("",!0)])])])],2)]),_:3})}const O=y(w,[["render",E]]);export{O as C};
