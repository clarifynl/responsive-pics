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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,e,i){i(1),i(2),t.exports=i(3)},function(t,e,i){var a="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(a)]},function(t,e){var i;(i=jQuery)(document).ready((function(){var t,e,a,n,o={width:0,height:0},c=function(t){console.log(t,t.target.id),e.addClass("is-dragging"),t.dataTransfer.setData("text",t.target.id),t.dataTransfer.effectAllowed="move"},r=function(t){console.log("draggingFocalPoint",t.target)},p=function(t){e.removeClass("is-dragging")},d=function(t){t.stopPropagation(),t.preventDefault(),t.dataTransfer.dropEffect="move"},s=function(t){t.stopPropagation(),t.preventDefault(),t.dataTransfer.getData("text"),console.log("dropFocalPoint",n.position())},l=function(i){var o=wp.media.template("attachment-select-focal-point"),c=i.find(".thumbnail"),r=i.find(".details-image");o&&(c.prepend(o),e=i.find(".image-focal"),a=i.find(".image-focal__wrapper"),n=i.find(".image-focal__point"),i.find(".image-focal__clickarea"),r.prependTo(a),t=a.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),d=i.find(".attachment-actions");p&&d.append(p)},f=function(e){var l,f,g=e.get("compat");if(g.item){var m=i(g.item).find(".compat-field-responsive_pics_focal_point_x input").val(),h=i(g.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=m,f=h,t.on("load",(function(t){o={width:i(t.currentTarget).width(),height:i(t.currentTarget).height()},a.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})})),n.css({left:"".concat(l,"%"),top:"".concat(f,"%"),display:"block"}),a.on("dragover",d),a.on("drop",s),n.on("dragstart",c),n.on("drag",r),n.on("dragend",p)}},g=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=g.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),f(this.model)),this},change:function(){"image"===this.model.attributes.type&&f(this.model)}})}))},function(t,e,i){}],[[0,1]]]);
//# sourceMappingURL=admin-a933d7ab.js.map