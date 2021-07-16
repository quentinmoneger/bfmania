/*==============================================================================
Init
==============================================================================*/
r.Enemy = function( opt ) {
	// set always and optional
	for( var k in opt ) {
		this[k] = opt[k];
	}

	// set optional and defaults
	this.lightness = r.util.isset( this.lightness ) ? this.lightness : 50;
	this.saturation = r.util.isset( this.saturation ) ? this.saturation : 100;
	this.setup = this.setup || function(){};
	this.death = this.death || function(){};

	// set same for all objects
	this.index = r.indexGlobal++;
	this.inView = this.hitFlag = this.vx = this.vy = 0;
	this.lifeMax = opt.life;
	this.fillStyle ='hsla(' + this.hue + ', ' + this.saturation + '%, ' + this.lightness + '%, 0.1)';
	this.strokeStyle = 'hsla(' + this.hue + ', ' + this.saturation + '%, ' + this.lightness + '%, 1)';
	/*==============================================================================
	Run Setup
	==============================================================================*/
	this.setup();

	/*==============================================================================
	Adjust Level Offset Difficulties
	==============================================================================*/
	if( r.levelDiffOffset > 0 ){
		this.life += r.levelDiffOffset * 0.25;
		this.lifeMax = this.life;
		this.speed += Math.min( r.hero.vmax, r.levelDiffOffset * 0.25 );
		this.value += r.levelDiffOffset * 5;
	}
};

/*==============================================================================
Update
==============================================================================*/
r.Enemy.prototype.update = function( i ) {
	/*==============================================================================
	Apply Behavior
	==============================================================================*/
	this.behavior();

	/*==============================================================================
	Apply Forces
	==============================================================================*/
	this.x += this.vx * r.dt;
	this.y += this.vy * r.dt;

	/*==============================================================================
	Lock Bounds
	==============================================================================*/
	if( this.lockBounds && !r.util.arcInRect( this.x, this.y, this.radius + 10, 0, 0, r.ww, r.wh ) ) {
		r.enemies.splice( i, 1 );
	}

	/*==============================================================================
	Update View
	==============================================================================*/
	if( r.util.arcInRect( this.x, this.y, this.radius, -r.screen.x, -r.screen.y, r.cw, r.ch ) ) {
		this.inView = 1;
	} else {
		this.inView = 0;
	}
};

/*==============================================================================
Receive Damage
==============================================================================*/
r.Enemy.prototype.receiveDamage = function( i, val ) {
	if( this.inView ) {
		r.audio.play( 'hit' );		
	}
	this.life -= val;
	this.hitFlag = 10;
	if( this.life <= 0 ) {
		if( this.inView ) {						
			r.explosions.push( new r.Explosion( {
				x: this.x,
				y: this.y,
				radius: this.radius,
				hue: this.hue,
				saturation: this.saturation
			} ) );
			r.particleEmitters.push( new r.ParticleEmitter( {
				x: this.x,
				y: this.y,
				count: 10,
				spawnRange: this.radius,
				friction: 0.85,
				minSpeed: 5,
				maxSpeed: 20,
				minDirection: 0,
				maxDirection: r.twopi,
				hue: this.hue,
				saturation: this.saturation
			} ) );
			r.textPops.push( new r.TextPop( {
				x: this.x,
				y: this.y,
				value: this.value,
				hue: this.hue,
				saturation: this.saturation,
				lightness: 60
			} ) );			
			r.rumble.level = 6;
		}
		this.death();
		r.spawnPowerup( this.x, this.y );
		r.score += this.value;
		r.level.kills++;
		r.kills++;
		r.enemies.splice( i, 1 );
	} 
};

/*==============================================================================
Render Health
==============================================================================*/
r.Enemy.prototype.renderHealth = function( i ) {
	if( this.inView && this.life > 0 && this.life < this.lifeMax ) {
		r.ctxmg.fillStyle = 'hsla(0, 0%, 0%, 0.75)';
		r.ctxmg.fillRect( this.x - this.radius, this.y - this.radius - 6, this.radius * 2, 3 );
		r.ctxmg.fillStyle = 'hsla(' + ( this.life / this.lifeMax ) * 120 + ', 100%, 50%, 0.75)';	
		r.ctxmg.fillRect( this.x - this.radius, this.y - this.radius - 6, ( this.radius * 2 ) * ( this.life / this.lifeMax ), 3 );
	}
};

/*==============================================================================
Render
==============================================================================*/
r.Enemy.prototype.render = function( i ) {
	if( this.inView ) {
		var mod = r.enemyOffsetMod / 6;
		r.util.fillCircle( r.ctxmg, this.x, this.y, this.radius, this.fillStyle );
		r.util.strokeCircle( r.ctxmg, this.x, this.y, this.radius / 4 + Math.cos( mod ) * this.radius / 4, this.strokeStyle, 1.5 );
		r.util.strokeCircle( r.ctxmg, this.x, this.y, this.radius - 0.5, this.strokeStyle, 1 );
		
		r.ctxmg.strokeStyle = this.strokeStyle;
		r.ctxmg.lineWidth = 4;
		r.ctxmg.beginPath();
		r.ctxmg.arc( this.x, this.y, this.radius - 0.5, mod + r.pi, mod + r.pi + r.pi / 2 );		
		r.ctxmg.stroke();
		r.ctxmg.beginPath();
		r.ctxmg.arc( this.x, this.y, this.radius - 0.5, mod, mod + r.pi / 2 );		
		r.ctxmg.stroke();

		if( r.slow) {
			r.util.fillCircle( r.ctxmg, this.x, this.y, this.radius, 'hsla(' + r.util.rand( 160, 220 ) + ', 100%, 50%, 0.25)' );
		} 
		if( this.hitFlag > 0 ) {
			this.hitFlag -= r.dt;
			r.util.fillCircle( r.ctxmg, this.x, this.y, this.radius, 'hsla(' + this.hue + ', ' + this.saturation + '%, 75%, ' + this.hitFlag / 10 + ')' );
			r.util.strokeCircle( r.ctxmg, this.x, this.y, this.radius, 'hsla(' + this.hue + ', ' + this.saturation + '%, ' + r.util.rand( 60, 90) + '%, ' + this.hitFlag / 10 + ')', r.util.rand( 1, 10) );	
		}
		this.renderHealth();
	}
};