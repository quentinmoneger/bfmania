/*==============================================================================
Init
==============================================================================*/
r.TextPop = function( opt ) {
	for( var k in opt ) {
		this[k] = opt[k];
	}
	this.alpha = 2;
	this.vy = 0;
};

/*==============================================================================
Update
==============================================================================*/
r.TextPop.prototype.update = function( i ) {
	this.vy -= 0.05;
	this.y += this.vy * r.dt;
	this.alpha -= 0.03 * r.dt;

	if( this.alpha <= 0 ){
		r.textPops.splice( i, 1 );
	}
};

/*==============================================================================
Render
==============================================================================*/
r.TextPop.prototype.render = function( i ) {
	r.ctxmg.beginPath();
	r.text( {
		ctx: r.ctxmg,
		x: this.x,
		y: this.y,
		text: '+' + this.value,
		hspacing: 1,
		vspacing: 0,
		halign: 'center',
		valign: 'center',
		scale: 2,
		snap: 0,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(' + this.hue + ', ' + this.saturation + '%, ' + this.lightness + '%, ' + this.alpha + ')';
	r.ctxmg.fill();
}