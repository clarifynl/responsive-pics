/*!
 * 
 * ResponsivePics
 * 
 * @author Booreiland
 * @version 1.4.0
 * @link https://responsive.pics
 * @license undefined
 * 
 * Copyright (c) 2021 Booreiland
 * 
 * This software is released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE)
 */
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,a,o={width:0,height:0},c=function(t){i.addClass("is-dragging")},p=function(t){console.log("draggingFocalPoint",t.target)},d=function(t){i.removeClass("is-dragging")},s=function(e){var o=wp.media.template("attachment-select-focal-point"),c=e.find(".thumbnail"),p=e.find(".details-image");o&&(c.prepend(o),i=e.find(".image-focal"),n=e.find(".image-focal__wrapper"),a=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),p.prependTo(n),t=n.find(".details-image"));var d=wp.media.template("attachment-save-focal-point"),s=e.find(".attachment-actions");d&&s.append(d)},r=function(i){var s,r,l=i.get("compat");if(l.item){var f=e(l.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=e(l.item).find(".compat-field-responsive_pics_focal_point_y input").val();s=f,r=m,t.on("load",(function(t){o={width:e(t.currentTarget).width(),height:e(t.currentTarget).height()},n.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})})),a.css({left:"".concat(s,"%"),top:"".concat(r,"%"),display:"block"}),a.on("dragstart",c),a.on("drag",p),a.on("dragend",d)}},l=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=l.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(s(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-551c088f.js.map