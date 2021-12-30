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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,o,a=function(t){i.addClass("is-dragging")},c=function(t){console.log("draggingFocalPoint",t.target)},p=function(t){i.removeClass("is-dragging")},s=function(t){t.stopPropagation(),t.preventDefault()},d=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(a){var c=wp.media.template("attachment-select-focal-point"),p=a.find(".thumbnail"),s=a.find(".details-image");console.log(s.width(),s.height()),s.on("load",(function(t){console.log(n(t.currentTarget).width(),n(t.currentTarget).height())})),c&&(p.prepend(c),i=a.find(".image-focal"),e=a.find(".image-focal__wrapper"),o=a.find(".image-focal__point"),a.find(".image-focal__clickarea"),s.prependTo(e),t=e.find(".details-image"));var d=wp.media.template("attachment-save-focal-point"),l=a.find(".attachment-actions");d&&l.append(d)},r=function(i){var l,r,f=i.get("compat");if(f.item){var g=n(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=n(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=g,r=m,console.log(t,t.width(),t.height()),e.css({width:t.width(),height:t.height()}),o.css({left:"".concat(l,"%"),top:"".concat(r,"%"),display:"block"}),e.on("dragover",s),e.on("drop",d),o.on("dragstart",a),o.on("drag",c),o.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-0b573cb6.js.map