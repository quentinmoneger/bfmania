/*==============================================================================
Init
==============================================================================*/
r.Powerup = function( opt ) {
	for( var k in opt ) {
		this[k] = opt[k];
	}
	var text = r.text( {
		ctx: r.ctxmg,
		x: 0,
		y: 0,
		text: this.title,
		hspacing: 1,
		vspacing: 0,
		halign: 'top',
		valign: 'left',
		scale: 1,
		snap: 0,
		render: 0
	} );
	this.hpadding = 8;
	this.vpadding = 8;
	this.width = text.width + this.hpadding * 2;
	this.height = text.height + this.vpadding * 2;
	this.x = this.x - this.width / 2;
	this.y = this.y - this.height / 2;
	this.direction = r.util.rand( 0, r.twopi );
	this.speed = r.util.rand( 0.5, 2 );
};

/*==============================================================================
Update
==============================================================================*/
r.Powerup.prototype.update = function( i ) {
	/*==============================================================================
	Apply Forces
	==============================================================================*/
	this.x += Math.cos( this.direction ) * this.speed * r.dt;
	this.y += Math.sin( this.direction ) * this.speed * r.dt;

	/*==============================================================================
	Check Bounds
	==============================================================================*/
	if( !r.util.rectInRect( this.x, this.y, this.width, this.height, 0, 0, r.ww, r.wh ) ){
		r.powerups.splice( i, 1 );
	}

	/*==============================================================================
	Check Collection Collision
	==============================================================================*/
	if( r.hero.life > 0 && r.util.arcIntersectingRect( r.hero.x, r.hero.y, r.hero.radius + 2, this.x, this.y, this.width, this.height ) ){
		r.audio.play( 'powerup' );
		r.powerupTimers[ this.type ] = 300;
		r.particleEmitters.push( new r.ParticleEmitter( {
			x: this.x + this.width / 2,
			y: this.y + this.height / 2,
			count: 15,
			spawnRange: 0,
			friction: 0.85,
			minSpeed: 2,
			maxSpeed: 15,
			minDirection: 0,
			maxDirection: r.twopi,
			hue: 0,
			saturation: 0
		} ) );
		r.powerups.splice( i, 1 );
		r.powerupsCollected++;
	}
};

/*==============================================================================
Render
==============================================================================*/
r.Powerup.prototype.render = function( i ) {

	r.ctxmg.fillStyle = '#000';
	r.ctxmg.fillRect( this.x - 2, this.y - 2, this.width + 4, this.height + 4 );
	r.ctxmg.fillStyle = '#555';
	r.ctxmg.fillRect( this.x - 1, this.y - 1, this.width + 2, this.height + 2 );
	
	r.ctxmg.fillStyle = '#111';
	r.ctxmg.fillRect( this.x, this.y, this.width, this.height );

	r.ctxmg.beginPath();
	r.text( {
		ctx: r.ctxmg,
		x: this.x + this.hpadding,
		y: this.y + this.vpadding + 1,
		text: this.title,
		hspacing: 1,
		vspacing: 0,
		halign: 'top',
		valign: 'left',
		scale: 1,
		snap: 0,
		render: true
	} );	
	r.ctxmg.fillStyle = '#000';
	r.ctxmg.fill();

	r.ctxmg.beginPath();
	r.text( {
		ctx: r.ctxmg,
		x: this.x + this.hpadding,
		y: this.y + this.vpadding,
		text: this.title,
		hspacing: 1,
		vspacing: 0,
		halign: 'top',
		valign: 'left',
		scale: 1,
		snap: 0,
		render: true
	} );	
	r.ctxmg.fillStyle = 'hsl(' + this.hue + ', ' + this.saturation + '%, ' + this.lightness + '%)';
	r.ctxmg.fill();

	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.2)';
	r.ctxmg.fillRect( this.x, this.y, this.width, this.height / 2 );
	
}