/*==============================================================================
Init
==============================================================================*/
r.LevelPop = function( opt ) {
	for( var k in opt ) {
		this[k] = opt[k];
	}
	this.x = r.cw - 20;
	this.y = r.ch - 20;
	this.tick = 0;
	this.tickMax = 240;
	this.baseAlpha = 0.2;
	if( r.tick != 0 ) {
		r.audio.play( 'levelup' );
	}
};

/*==============================================================================
Update
==============================================================================*/
r.LevelPop.prototype.update = function( i ) {
	if( this.tick >= this.tickMax ) {
		r.levelPops.splice( i, 1 );
	} else {
		this.tick += r.dt;
	}
};

/*==============================================================================
Render
==============================================================================*/
r.LevelPop.prototype.render = function( i ) {
	r.ctxmg.beginPath();
	r.text( {
		ctx: r.ctxmg,
		x: this.x,
		y: this.y,
		text: r.util.pad( this.level, 2 ),
		hspacing: 3,
		vspacing: 0,
		halign: 'right',
		valign: 'bottom',
		scale: 12,
		snap: 1,
		render: 1
	} );
	if( this.tick < this.tickMax * 0.25 ) {
		var alpha = ( this.tick / ( this.tickMax * 0.25 ) ) * this.baseAlpha;
	} else if( this.tick > this.tickMax - this.tickMax * 0.25 ) {
		var alpha = ( ( this.tickMax - this.tick ) / ( this.tickMax * 0.25 ) ) * this.baseAlpha;
	} else {
		var alpha = this.baseAlpha;
	}
	alpha = Math.min( 1, Math.max( 0, alpha ) );
	
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, ' + alpha + ')';
	r.ctxmg.fill();
}