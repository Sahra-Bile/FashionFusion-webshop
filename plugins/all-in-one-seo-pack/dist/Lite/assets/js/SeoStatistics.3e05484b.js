import{A as P}from"./links.d8ef3c22.js";import{C as B}from"./Blur.71007c0c.js";import{C as L}from"./Card.a96d9a32.js";import{G,S as U}from"./SeoStatisticsOverview.ba180032.js";import{G as O,a as q}from"./Row.7b93a0cf.js";import{P as A}from"./PostsTable.4eefa994.js";import{r as s,o as l,b as h,w as o,a as y,d as i,g as m,t as _,c as v,f as w}from"./vue.runtime.esm-bundler.0bc3eabf.js";import{_ as f}from"./_plugin-vue_export-helper.8823f7c1.js";import{C as E}from"./Index.38afdc86.js";import{R as N}from"./RequiredPlans.d0936efa.js";import"./index.888aa896.js";import"./Caret.11ded1aa.js";/* empty css                                            *//* empty css                                              */import"./default-i18n.3881921e.js";import"./constants.d0e2b74f.js";import{L as R}from"./Statistic.a3665f76.js";import"./TruSeoHighlighter.f41d03f0.js";/* empty css                                              */import"./isArrayLikeObject.10b615a9.js";import"./Tooltip.eebec260.js";import"./Slide.c3dfb2b1.js";import"./numbers.c7cb4085.js";import"./WpTable.de7a11dd.js";import"./PostTypes.9ab32454.js";import"./ScoreButton.100cb91e.js";import"./Table.4899793d.js";import"./addons.ecfd02c6.js";import"./upperFirst.d65414ba.js";import"./_stringToArray.a4422725.js";import"./license.db04cc67.js";import"./_arrayEach.56a9f647.js";import"./_getAllKeys.4291a623.js";import"./_getTag.4ca3d6f0.js";import"./vue.runtime.esm-bundler.411b6122.js";import"./postContent.5b10ed83.js";import"./cleanForSlug.a67f7e84.js";import"./Ellipse.e18bc40f.js";import"./toFinite.c2274946.js";const H={setup(){return{searchStatisticsStore:P()}},components:{CoreBlur:B,CoreCard:L,Graph:G,GridColumn:O,GridRow:q,PostsTable:A,SeoStatisticsOverview:U},data(){return{strings:{seoStatisticsCard:this.$t.__("SEO Statistics",this.$td),seoStatisticsTooltip:this.$t.__("The following SEO Statistics graphs are useful metrics for understanding the visibility of your website or pages in search results and can help you identify trends or changes over time.<br /><br />Note: This data is capped at the top 100 keywords per day to speed up processing and to help you prioritize your SEO efforts, so while the data may seem inconsistent with Google Search Console, this is intentional.",this.$td),contentCard:this.$t.__("Content",this.$td),postsTooltip:this.$t.__("These lists can be useful for understanding the performance of specific pages or posts and identifying opportunities for improvement. For example, the top winning content may be good candidates for further optimization or promotion, while the top losing may need to be reevaluated and potentially updated.",this.$td)},defaultPages:{rows:[],totals:{page:0,pages:0,total:0}}}},computed:{series(){var e,a,n,r;return!((a=(e=this.searchStatisticsStore.data)==null?void 0:e.seoStatistics)!=null&&a.statistics)||!((r=(n=this.searchStatisticsStore.data)==null?void 0:n.seoStatistics)!=null&&r.intervals)?[]:[{name:this.$t.__("Search Impressions",this.$td),data:this.searchStatisticsStore.data.seoStatistics.intervals.map(t=>({x:t.date,y:t.impressions})),legend:{total:this.searchStatisticsStore.data.seoStatistics.statistics.impressions}},{name:this.$t.__("Search Clicks",this.$td),data:this.searchStatisticsStore.data.seoStatistics.intervals.map(t=>({x:t.date,y:t.clicks})),legend:{total:this.searchStatisticsStore.data.seoStatistics.statistics.clicks}}]}}},I={class:"aioseo-search-statistics-dashboard"},V=["innerHTML"];function D(e,a,n,r,t,u){const c=s("seo-statistics-overview"),p=s("graph"),d=s("core-card"),k=s("posts-table"),x=s("grid-column"),C=s("grid-row"),T=s("core-blur");return l(),h(T,null,{default:o(()=>[y("div",I,[i(C,null,{default:o(()=>[i(x,null,{default:o(()=>[i(d,{class:"aioseo-seo-statistics-card",slug:"seoPerformance","header-text":t.strings.seoStatisticsCard,toggles:!1,"no-slide":""},{tooltip:o(()=>[y("span",{innerHTML:t.strings.seoStatisticsTooltip},null,8,V)]),default:o(()=>[i(c,{statistics:["impressions","clicks","ctr","position"],"show-graph":!1,view:"side-by-side"}),i(p,{"multi-axis":"",series:u.series,"legend-style":"simple"},null,8,["series"])]),_:1},8,["header-text"]),i(d,{slug:"posts","header-text":t.strings.contentCard,toggles:!1,"no-slide":""},{tooltip:o(()=>[m(_(t.strings.postsTooltip),1)]),default:o(()=>{var g,S,$;return[i(k,{posts:(($=(S=(g=r.searchStatisticsStore.data)==null?void 0:g.seoStatistics)==null?void 0:S.pages)==null?void 0:$.paginated)||t.defaultPages,columns:["postTitle","seoScore","clicksSortable","impressionsSortable","positionSortable","diffPositionSortable"],"show-items-per-page":"","show-table-footer":""},null,8,["posts"])]}),_:1},8,["header-text"])]),_:1})]),_:1})])]),_:1})}const M=f(H,[["render",D]]);const z={components:{Blur:M,Cta:E,RequiredPlans:N},data(){return{strings:{ctaButtonText:this.$t.sprintf(this.$t.__("Upgrade to %1$s and Unlock Search Statistics",this.$td),"Pro"),ctaHeader:this.$t.sprintf(this.$t.__("Search Statistics is only for licensed %1$s %2$s users.",this.$td),"AIOSEO","Pro"),ctaDescription:this.$t.__("Connect your site to Google Search Console to receive insights on how content is being discovered. Identify areas for improvement and drive traffic to your website.",this.$td),thisFeatureRequires:this.$t.__("This feature requires one of the following plans:",this.$td),feature1:this.$t.__("Search traffic insights",this.$td),feature2:this.$t.__("Track page rankings",this.$td),feature3:this.$t.__("Track keyword rankings",this.$td),feature4:this.$t.__("Speed tests for individual pages/posts",this.$td)}}}},F={class:"aioseo-search-statistics-seo-statistics"};function j(e,a,n,r,t,u){const c=s("blur"),p=s("required-plans"),d=s("cta");return l(),v("div",F,[i(c),i(d,{"cta-link":e.$links.getPricingUrl("search-statistics","search-statistics-upsell","seo-statistics"),"button-text":t.strings.ctaButtonText,"learn-more-link":e.$links.getUpsellUrl("search-statistics","seo-statistics","home"),"feature-list":[t.strings.feature1,t.strings.feature2,t.strings.feature3,t.strings.feature4],"align-top":""},{"header-text":o(()=>[m(_(t.strings.ctaHeader),1)]),description:o(()=>[i(p,{"core-feature":["search-statistics","seo-statistics"]}),m(" "+_(t.strings.ctaDescription),1)]),_:1},8,["cta-link","button-text","learn-more-link","feature-list"])])}const b=f(z,[["render",j]]),J={mixins:[R],components:{SeoStatistics:b,Lite:b}},K={class:"aioseo-search-statistics-seo-statistics"};function Q(e,a,n,r,t,u){const c=s("seo-statistics",!0),p=s("lite");return l(),v("div",K,[e.shouldShowMain("search-statistics","seo-statistics")?(l(),h(c,{key:0})):w("",!0),e.shouldShowUpgrade("search-statistics","seo-statistics")||e.shouldShowLite?(l(),h(p,{key:1})):w("",!0)])}const At=f(J,[["render",Q]]);export{At as default};
