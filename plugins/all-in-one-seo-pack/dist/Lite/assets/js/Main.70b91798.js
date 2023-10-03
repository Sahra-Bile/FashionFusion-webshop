import{b as w,A as y,d as L,D as c}from"./links.d8ef3c22.js";import{l as f}from"./license.db04cc67.js";import{C as M}from"./index.888aa896.js";import{r as o,o as a,b as l,w as n,a as C,t as d,f as u,c as _,d as p,D as S,g as D}from"./vue.runtime.esm-bundler.0bc3eabf.js";import{_ as h}from"./_plugin-vue_export-helper.8823f7c1.js";import{B as T}from"./DatePicker.b35e138a.js";import{C as B}from"./Blur.71007c0c.js";import{C as A}from"./Index.c396e6bb.js";import O from"./ContentRankings.37c6b7ee.js";import{C as J}from"./Index.38afdc86.js";import R from"./Dashboard.4951725e.js";import N from"./KeywordRankings.c18d8831.js";import P from"./SeoStatistics.3e05484b.js";import"./default-i18n.3881921e.js";import"./isArrayLikeObject.10b615a9.js";import"./upperFirst.d65414ba.js";import"./_stringToArray.a4422725.js";import"./Caret.11ded1aa.js";import"./cleanForSlug.a67f7e84.js";import"./isUndefined.1db526df.js";import"./_getAllKeys.4291a623.js";import"./_getTag.4ca3d6f0.js";import"./TruSeoHighlighter.f41d03f0.js";import"./postContent.5b10ed83.js";import"./Ellipse.e18bc40f.js";import"./toFinite.c2274946.js";import"./isEqual.585d298d.js";import"./_baseIsEqual.640c1807.js";import"./allowed.a855ba11.js";/* empty css             */import"./params.f0608262.js";/* empty css                                            *//* empty css                                              */import"./constants.d0e2b74f.js";import"./SaveChanges.5260e1c7.js";/* empty css                                              */import"./Header.06623042.js";import"./addons.ecfd02c6.js";import"./LicenseKeyBar.aa0cbefb.js";import"./LogoGear.5cfaa040.js";import"./AnimatedNumber.9020942d.js";import"./numbers.c7cb4085.js";import"./Logo.733f1a87.js";import"./Support.dd2dc8c2.js";import"./Tabs.17b2f5c8.js";import"./TruSeoScore.29220195.js";import"./Information.0dce27f3.js";import"./Slide.c3dfb2b1.js";import"./Date.75340b8b.js";import"./Exclamation.0dd78a69.js";import"./Url.831daf51.js";import"./Gear.93d6eb68.js";import"./Row.7b93a0cf.js";import"./PostsTable.4eefa994.js";import"./WpTable.de7a11dd.js";import"./PostTypes.9ab32454.js";import"./Statistic.a3665f76.js";import"./_arrayEach.56a9f647.js";import"./vue.runtime.esm-bundler.411b6122.js";import"./Tooltip.eebec260.js";import"./ScoreButton.100cb91e.js";import"./Table.4899793d.js";import"./RequiredPlans.d0936efa.js";import"./Card.a96d9a32.js";import"./Overview.bd23debf.js";import"./DonutChartWithLegend.bb40445e.js";import"./KeywordsGraph.8a9cdd4c.js";import"./SeoStatisticsOverview.ba180032.js";import"./List.677b58a9.js";import"./Statistics.1a22be2e.js";const V={setup(){return{optionsStore:w(),searchStatisticsStore:y()}},components:{CoreAlert:M},data(){return{error:this.$t.__("Your connection with Google Search Console has expired or is invalid. Please check that your site is verified in Google Search Console and try to reconnect. If the problem persists, please contact our support team.",this.$td)}},computed:{invalidAuthentication(){var t,s;return this.searchStatisticsStore.unverifiedSite||typeof((s=(t=this.optionsStore.internalOptions.internal)==null?void 0:t.searchStatistics)==null?void 0:s.profile)!="object"}}};function G(t,s,i,g,r,e){const m=o("core-alert");return e.invalidAuthentication?(a(),l(m,{key:0,class:"aioseo-input-error aioseo-search-statistics-authentication-alert",type:"red"},{default:n(()=>[C("strong",null,d(r.error),1)]),_:1})):u("",!0)}const U=h(V,[["render",G]]),F={};function I(t,s){return a(),_("div")}const z=h(F,[["render",I]]),E={};function H(t,s){return a(),_("div")}const j=h(E,[["render",H]]);const q={setup(){return{licenseStore:L(),searchStatisticsStore:y()}},emits:["rolling"],components:{AuthenticationAlert:U,BaseDatePicker:T,CoreBlur:B,CoreMain:A,ContentRankings:O,Cta:J,Dashboard:R,KeywordRankings:N,PostDetail:z,Settings:j,SeoStatistics:P},data(){return{maxDate:null,minDate:null,loadingConnect:!1,strings:{pageName:this.$t.__("Search Statistics",this.$td),ctaHeaderText:this.$t.__("Connect your website to Google Search Console",this.$td),ctaDescription:this.$t.__("Connect to Google Search Console to receive valuable insights on how your content is being discovered. Identify areas for improvement in order to improve search engine rankings and drive more traffic to your website.",this.$td),ctaButtonText:this.$t.__("Connect to Google Search Console",this.$td),feature1:this.$t.__("Search traffic insights",this.$td),feature2:this.$t.__("Improved visibility",this.$td),feature3:this.$t.__("Track page and keyword rankings",this.$td),feature4:this.$t.__("Speed tests for individual pages/posts",this.$td)}}},computed:{defaultRange(){const t=new Date(`${this.searchStatisticsStore.range.start} 00:00:00`),s=new Date(`${this.searchStatisticsStore.range.end} 00:00:00`);return[t,s]},excludeTabs(){const t=["post-detail"];return(this.licenseStore.isUnlicensed||!f.hasCoreFeature("search-statistics"))&&t.push("settings"),t},isSettings(){return this.$route.name==="settings"},showConnectCta(){return(f.hasCoreFeature("search-statistics")&&!this.searchStatisticsStore.isConnected||this.searchStatisticsStore.unverifiedSite)&&!this.isSettings},showDatePicker(){return!["settings","content-rankings"].includes(this.$route.name)&&this.searchStatisticsStore.isConnected&&!this.searchStatisticsStore.unverifiedSite},containerClasses(){const t=[];return this.searchStatisticsStore.fetching&&t.push("aioseo-blur"),t},getOriginalMaxDate(){return this.searchStatisticsStore.latestAvailableDate?c.fromFormat(this.searchStatisticsStore.latestAvailableDate,"yyyy-MM-dd").setZone(c.zone)||c.local().plus({days:-2}):c.local().plus({days:-2})},datepickerShortcuts(){return[{text:this.$t.__("Last 7 Days",this.$td),value:()=>(window.aioseoBus.$emit("rolling","last7Days"),[this.getOriginalMaxDate.plus({days:-6}).toJSDate(),this.getOriginalMaxDate.toJSDate()])},{text:this.$t.__("Last 28 Days",this.$td),value:()=>(window.aioseoBus.$emit("rolling","last28Days"),[this.getOriginalMaxDate.plus({days:-27}).toJSDate(),this.getOriginalMaxDate.toJSDate()])},{text:this.$t.__("Last 3 Months",this.$td),value:()=>(window.aioseoBus.$emit("rolling","last3Months"),[this.getOriginalMaxDate.plus({days:-89}).toJSDate(),this.getOriginalMaxDate.toJSDate()])}]}},methods:{isDisabledDate(t){return this.minDate===null?!0:t.getTime()<this.minDate.getTime()||t.getTime()>this.maxDate.getTime()},onDateChange(t,s){this.searchStatisticsStore.setDateRange({dateRange:t,rolling:s})},connect(){this.loadingConnect=!0,this.searchStatisticsStore.getAuthUrl().then(t=>{window.location=t})},highlightShortcut(t){if(!t)return;document.querySelectorAll(".el-picker-panel__shortcut").forEach(i=>{switch(i.innerText){case this.$t.__("Last 7 Days",this.$td):t==="last7Days"?i.classList.add("active"):i.classList.remove("active");break;case this.$t.__("Last 28 Days",this.$td):t==="last28Days"?i.classList.add("active"):i.classList.remove("active");break;case this.$t.__("Last 3 Months",this.$td):t==="last3Months"?i.classList.add("active"):i.classList.remove("active");break;case this.$t.__("Last 6 Months",this.$td):t==="last6Months"?i.classList.add("active"):i.classList.remove("active");break;default:i.classList.remove("active")}})}},mounted(){this.minDate=c.now().plus({months:-16}).toJSDate(),this.maxDate=this.getOriginalMaxDate.toJSDate()}},K={key:0,class:"connect-cta"};function Y(t,s,i,g,r,e){const m=o("base-date-picker"),v=o("authentication-alert"),x=o("core-blur"),$=o("cta"),k=o("core-main");return a(),l(k,{"page-name":r.strings.pageName,"exclude-tabs":e.excludeTabs,showTabs:!e.excludeTabs.includes(t.$route.name),containerClasses:e.containerClasses},{extra:n(()=>[e.showDatePicker?(a(),l(m,{key:0,onChange:e.onDateChange,onUpdated:s[0]||(s[0]=b=>e.highlightShortcut(b)),defaultValue:e.defaultRange,defaultRolling:g.searchStatisticsStore.rolling,isDisabledDate:e.isDisabledDate,shortcuts:e.datepickerShortcuts,size:"small"},null,8,["onChange","defaultValue","defaultRolling","isDisabledDate","shortcuts"])):u("",!0)]),default:n(()=>[C("div",null,[p(v),e.showConnectCta?(a(),_("div",K,[p(x,null,{default:n(()=>[(a(),l(S(t.$route.name)))]),_:1}),p($,{"cta-button-action":"",onCtaButtonClick:e.connect,"cta-button-loading":r.loadingConnect,"show-link":!1,"button-text":r.strings.ctaButtonText,alignTop:!0,"feature-list":[r.strings.feature1,r.strings.feature2,r.strings.feature3,r.strings.feature4]},{"header-text":n(()=>[D(d(r.strings.ctaHeaderText),1)]),description:n(()=>[D(d(r.strings.ctaDescription),1)]),_:1},8,["onCtaButtonClick","cta-button-loading","button-text","feature-list"])])):u("",!0),e.showConnectCta?u("",!0):(a(),l(S(t.$route.name),{key:1}))])]),_:1},8,["page-name","exclude-tabs","showTabs","containerClasses"])}const me=h(q,[["render",Y]]);export{me as default};