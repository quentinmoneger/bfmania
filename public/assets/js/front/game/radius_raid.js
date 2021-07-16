/*==============================================================================
Init
==============================================================================*/
r.init = function() {
	r.setupStorage();
	r.wrap = document.getElementById( 'wrap' );
	r.wrapInner = document.getElementById( 'wrap-inner' );
	r.cbg1 = document.getElementById( 'cbg1' );
	r.cbg2 = document.getElementById( 'cbg2' );
	r.cbg3 = document.getElementById( 'cbg3' );
	r.cbg4 = document.getElementById( 'cbg4' );
	r.cmg = document.getElementById( 'cmg' );
	r.cfg = document.getElementById( 'cfg' );	
	r.ctxbg1 = r.cbg1.getContext( '2d' );
	r.ctxbg2 = r.cbg2.getContext( '2d' );
	r.ctxbg3 = r.cbg3.getContext( '2d' );
	r.ctxbg4 = r.cbg4.getContext( '2d' );
	r.ctxmg = r.cmg.getContext( '2d' );
	r.ctxfg = r.cfg.getContext( '2d' );
	r.cw = r.cmg.width = r.cfg.width = 800;
	r.ch = r.cmg.height = r.cfg.height = 600;
	r.wrap.style.width = r.wrapInner.style.width = r.cw + 'px';
	r.wrap.style.height = r.wrapInner.style.height = r.ch + 'px';
	r.wrap.style.marginLeft = ( -r.cw / 2 ) - 10 + 'px';
	r.wrap.style.marginTop = ( -r.ch / 2 ) - 10 + 'px';
	r.ww = Math.floor( r.cw * 2 );
	r.wh = Math.floor( r.ch * 2 );
	r.cbg1.width = Math.floor( r.cw * 1.1 );
	r.cbg1.height = Math.floor( r.ch * 1.1 );
	r.cbg2.width = Math.floor( r.cw * 1.15 );
	r.cbg2.height = Math.floor( r.ch * 1.15 );
	r.cbg3.width = Math.floor( r.cw * 1.2 );
	r.cbg3.height = Math.floor( r.ch * 1.2 );
	r.cbg4.width = Math.floor( r.cw * 1.25 );
	r.cbg4.height = Math.floor( r.ch * 1.25 );

	r.screen = {
		x: ( r.ww - r.cw ) / -2,
		y: ( r.wh - r.ch ) / -2
	};

	r.mute = r.storage['mute'];
	r.autofire = r.storage['autofire'];
	r.slowEnemyDivider = 3;	

	r.keys = {
		state: {
			up: 0,
			down: 0,
			left: 0,
			right: 0,
			f: 0,
			m: 0,
			p: 0
		},
		pressed: {
			up: 0,
			down: 0,
			left: 0,
			right: 0,
			f: 0,
			m: 0,
			p: 0
		}
	};
	r.okeys = {};
	r.mouse = {
		x: r.ww / 2,
		y: r.wh / 2,
		sx: 0,
		sy: 0,
		ax: window.innerWidth / 2,
		ay: 0,
		down: 0
	};
	r.buttons = [];

	r.minimap = {		
		x: 20,
		y: r.ch - Math.floor( r.ch * 0.1 ) - 20,
		width: Math.floor( r.cw * 0.1 ),
		height: Math.floor( r.ch * 0.1 ),
		scale: Math.floor( r.cw * 0.1 ) / r.ww,
		color: 'hsla(0, 0%, 0%, 0.85)',
		strokeColor: '#3a3a3a'
	},	
	r.cOffset = { 
		left: 0, 
		top: 0 
	};
	
	r.levelCount = r.definitions.levels.length;
	r.states = {};
	r.state = '';
	r.enemies = [];
	r.bullets = [];
	r.explosions = [];
	r.powerups = [];	
	r.particleEmitters = [];
	r.textPops = [];
	r.levelPops = [];
	r.powerupTimers = [];

	r.resizecb();
	r.bindEvents();
	r.setupStates();	
	r.renderBackground1();
	r.renderBackground2();
	r.renderBackground3();
	r.renderBackground4();
	r.renderForeground();
	r.renderFavicon();
	r.setState( 'menu' );
	r.loop();
};

/*==============================================================================
Reset
==============================================================================*/
r.reset = function() {
	r.indexGlobal = 0;
	r.dt = 1;
	r.lt = 0;
	r.elapsed = 0;
	r.tick = 0;

	r.gameoverTick = 0;
	r.gameoverTickMax = 200;
	r.gameoverExplosion = 0;

	r.instructionTick = 0;
	r.instructionTickMax = 400;

	r.levelDiffOffset = 0;
	r.enemyOffsetMod = 0;
	r.slow = 0;

	r.screen = {
		x: ( r.ww - r.cw ) / -2,
		y: ( r.wh - r.ch ) / -2
	};
	r.rumble = {
		x: 0,
		y: 0,
		level: 0,
		decay: 0.4
	};	

	r.mouse.down = 0;

	r.level = {
		current: 0,
		kills: 0,
		killsToLevel: r.definitions.levels[ 0 ].killsToLevel,
		distribution: r.definitions.levels[ 0 ].distribution,
		distributionCount: r.definitions.levels[ 0 ].distribution.length
	};

	r.enemies.length = 0;
	r.bullets.length = 0;
	r.explosions.length = 0;
	r.powerups.length = 0;
	r.particleEmitters.length = 0;
	r.textPops.length = 0;
	r.levelPops.length = 0;
	r.powerupTimers.length = 0;

	for( var i = 0; i < r.definitions.powerups.length; i++ ) {
		r.powerupTimers.push( 0 );
	}

	r.kills = 0;
	r.bulletsFired = 0;
	r.powerupsCollected = 0;
	r.score = 0;

	r.hero = new r.Hero();

	r.levelPops.push( new r.LevelPop( {
		level: 1
	} ) );
};

/*==============================================================================
Create Favicon
==============================================================================*/
r.renderFavicon = function() {
	var favicon = document.getElementById( 'favicon' ),
		favc = document.createElement( 'canvas' ),
		favctx = favc.getContext( '2d' ),
		faviconGrid = [
			[ 1, 1, 1, 1, 1,  ,  , 1, 1, 1, 1, 1, 1, 1, 1, 1 ],
			[ 1,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1, 1, 1, 1,  , 0 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1, 1, 1, 1,  , 0 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  , 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[  ,  , 1, 1, 1, 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[  ,  , 1, 1, 1, 1, 1,  ,  , 1, 1,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  , 1 ],
			[ 1,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  ,  , 1 ],
			[ 1, 1, 1, 1, 1, 1, 1, 1, 1,  ,  , 1, 1, 1, 1, 1 ]
		];
	favc.width = favc.height = 16;
	favctx.beginPath();
	for( var y = 0; y < 16; y++ ) {
		for( var x = 0; x < 16; x++ ) {
			if( faviconGrid[ y ][ x ] === 1 ) {
				favctx.rect( x, y, 1, 1 );
			}
		}
	}
	favctx.fill();
	favicon.href = favc.toDataURL();
};

/*==============================================================================
Render Backgrounds
==============================================================================*/
r.renderBackground1 = function() {
	var gradient = r.ctxbg1.createRadialGradient( r.cbg1.width / 2, r.cbg1.height / 2, 0, r.cbg1.width / 2, r.cbg1.height / 2, r.cbg1.height );
	gradient.addColorStop( 0, 'hsla(0, 0%, 100%, 0.1)' );
	gradient.addColorStop( 0.65, 'hsla(0, 0%, 100%, 0)' );
	r.ctxbg1.fillStyle = gradient;
	r.ctxbg1.fillRect( 0, 0, r.cbg1.width, r.cbg1.height );

	var i = 2000;
	while( i-- ) {
		r.util.fillCircle( r.ctxbg1, r.util.rand( 0, r.cbg1.width ), r.util.rand( 0, r.cbg1.height ), r.util.rand( 0.2, 0.5 ), 'hsla(0, 0%, 100%, ' + r.util.rand( 0.05, 0.2 ) + ')' );
	}

	var i = 800;
	while( i-- ) {
		r.util.fillCircle( r.ctxbg1, r.util.rand( 0, r.cbg1.width ), r.util.rand( 0, r.cbg1.height ), r.util.rand( 0.1, 0.8 ), 'hsla(0, 0%, 100%, ' + r.util.rand( 0.05, 0.5 ) + ')' );
	}
}

r.renderBackground2 = function() {
	var i = 80;
	while( i-- ) {
		r.util.fillCircle( r.ctxbg2, r.util.rand( 0, r.cbg2.width ), r.util.rand( 0, r.cbg2.height ), r.util.rand( 1, 2 ), 'hsla(0, 0%, 100%, ' + r.util.rand( 0.05, 0.15 ) + ')' );
	}
}

r.renderBackground3 = function() {
	var i = 40;
	while( i-- ) {
		r.util.fillCircle( r.ctxbg3, r.util.rand( 0, r.cbg3.width ), r.util.rand( 0, r.cbg3.height ), r.util.rand( 1, 2.5 ), 'hsla(0, 0%, 100%, ' + r.util.rand( 0.05, 0.1 ) + ')' );
	}
}

r.renderBackground4 = function() {
	var size = 50;
	r.ctxbg4.fillStyle = 'hsla(0, 0%, 50%, 0.05)';
	var i = Math.round( r.cbg4.height / size );
	while( i-- ) {
		r.ctxbg4.fillRect( 0, i * size + 25, r.cbg4.width, 1 );
	}
	i = Math.round( r.cbg4.width / size );
	while( i-- ) {
		r.ctxbg4.fillRect( i * size, 0, 1, r.cbg4.height );
	}
}

/*==============================================================================
Render Foreground
==============================================================================*/
r.renderForeground = function() {
	var gradient = r.ctxfg.createRadialGradient( r.cw / 2, r.ch / 2, r.ch / 3, r.cw / 2, r.ch / 2, r.ch );
	gradient.addColorStop( 0, 'hsla(0, 0%, 0%, 0)' );
	gradient.addColorStop( 1, 'hsla(0, 0%, 0%, 0.5)' );
	r.ctxfg.fillStyle = gradient;
	r.ctxfg.fillRect( 0, 0, r.cw, r.ch );

	r.ctxfg.fillStyle = 'hsla(0, 0%, 50%, 0.1)';
	var i = Math.round( r.ch / 2 );
	while( i-- ) {
		r.ctxfg.fillRect( 0, i * 2, r.cw, 1 );
	}

	var gradient2 = r.ctxfg.createLinearGradient( r.cw, 0, 0, r.ch );
	gradient2.addColorStop( 0, 'hsla(0, 0%, 100%, 0.04)' );
	gradient2.addColorStop( 0.75, 'hsla(0, 0%, 100%, 0)' );
	r.ctxfg.beginPath();
	r.ctxfg.moveTo( 0, 0 );
	r.ctxfg.lineTo( r.cw, 0 );
	r.ctxfg.lineTo( 0, r.ch );
	r.ctxfg.closePath();
	r.ctxfg.fillStyle = gradient2;
	r.ctxfg.fill();
}

/*==============================================================================
User Interface / UI / GUI / Minimap
==============================================================================*/

r.renderInterface = function() {
	/*==============================================================================
	Powerup Timers
	==============================================================================*/
		for( var i = 0; i < r.definitions.powerups.length; i++ ) {
			var powerup = r.definitions.powerups[ i ],
				powerupOn = ( r.powerupTimers[ i ] > 0 );
			r.ctxmg.beginPath();
			var powerupText = r.text( {
				ctx: r.ctxmg,
				x: r.minimap.x + r.minimap.width + 90,
				y: r.minimap.y + 4 + ( i * 12 ),
				text: powerup.title,
				hspacing: 1,
				vspacing: 1,
				halign: 'right',
				valign: 'top',
				scale: 1,
				snap: 1,
				render: 1
			} );
			if( powerupOn ) {
				r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, ' + ( 0.25 + ( ( r.powerupTimers[ i ] / 300 ) * 0.75 ) ) + ')';
			} else {
				r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.25)';
			}
			r.ctxmg.fill();
			if( powerupOn ) {
				var powerupBar = {
					x: powerupText.ex + 5,
					y: powerupText.sy,
					width: 110,
					height: 5
				};
				r.ctxmg.fillStyle = 'hsl(' + powerup.hue + ', ' + powerup.saturation + '%, ' + powerup.lightness + '%)';
				r.ctxmg.fillRect( powerupBar.x, powerupBar.y, ( r.powerupTimers[ i ] / 300 ) * powerupBar.width, powerupBar.height );
			}
		}

		/*==============================================================================
		Instructions
		==============================================================================*/
		if( r.instructionTick < r.instructionTickMax ){
			r.instructionTick += r.dt;
			r.ctxmg.beginPath();
			r.text( {
				ctx: r.ctxmg,
				x: r.cw / 2 - 10,
				y: r.ch - 20,
				text: 'MOVE\nAIM/FIRE\nAUTOFIRE\nPAUSE\nMUTE',
				hspacing: 1,
				vspacing: 17,
				halign: 'right',
				valign: 'bottom',
				scale: 2,
				snap: 1,
				render: 1
			} );
			if( r.instructionTick < r.instructionTickMax * 0.25 ) {
				var alpha = ( r.instructionTick / ( r.instructionTickMax * 0.25 ) ) * 0.5;
			} else if( r.instructionTick > r.instructionTickMax - r.instructionTickMax * 0.25 ) {
				var alpha = ( ( r.instructionTickMax - r.instructionTick ) / ( r.instructionTickMax * 0.25 ) ) * 0.5;
			} else {
				var alpha = 0.5;
			}
			alpha = Math.min( 1, Math.max( 0, alpha ) );
			
			r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, ' + alpha + ')';
			r.ctxmg.fill();

			r.ctxmg.beginPath();
			r.text( {
				ctx: r.ctxmg,
				x: r.cw / 2 + 10,
				y: r.ch - 20,
				text: 'WASD/ARROWS\nMOUSE\nF\nP\nM',
				hspacing: 1,
				vspacing: 17,
				halign: 'left',
				valign: 'bottom',
				scale: 2,
				snap: 1,
				render: 1
			} );
			if( r.instructionTick < r.instructionTickMax * 0.25 ) {
				var alpha = ( r.instructionTick / ( r.instructionTickMax * 0.25 ) ) * 1;
			} else if( r.instructionTick > r.instructionTickMax - r.instructionTickMax * 0.25 ) {
				var alpha = ( ( r.instructionTickMax - r.instructionTick ) / ( r.instructionTickMax * 0.25 ) ) * 1;
			} else {
				var alpha = 1;
			}
			alpha = Math.min( 1, Math.max( 0, alpha ) );
			
			r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, ' + alpha + ')';
			r.ctxmg.fill();
		}

		/*==============================================================================
		Slow Enemies Screen Cover
		==============================================================================*/
		if( r.powerupTimers[ 1 ] > 0 ) {
			r.ctxmg.fillStyle = 'hsla(200, 100%, 20%, 0.05)';
			r.ctxmg.fillRect( 0, 0, r.cw, r.ch );
		}

	/*==============================================================================
	Health
	==============================================================================*/
	r.ctxmg.beginPath();
	var healthText = r.text( {
		ctx: r.ctxmg,
		x: 20,
		y: 20,
		text: 'HEALTH',
		hspacing: 1,
		vspacing: 1,
		halign: 'top',
		valign: 'left',
		scale: 2,
		snap: 1,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
	r.ctxmg.fill();
	var healthBar = {
		x: healthText.ex + 10,
		y: healthText.sy,
		width: 110,
		height: 10
	};
	r.ctxmg.fillStyle = 'hsla(0, 0%, 20%, 1)';
	r.ctxmg.fillRect( healthBar.x, healthBar.y, healthBar.width, healthBar.height );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.25)';
	r.ctxmg.fillRect( healthBar.x, healthBar.y, healthBar.width, healthBar.height / 2 );
	r.ctxmg.fillStyle = 'hsla(' + r.hero.life * 120 + ', 100%, 40%, 1)';
	r.ctxmg.fillRect( healthBar.x, healthBar.y, r.hero.life * healthBar.width, healthBar.height );
	r.ctxmg.fillStyle = 'hsla(' + r.hero.life * 120 + ', 100%, 75%, 1)';
	r.ctxmg.fillRect( healthBar.x, healthBar.y, r.hero.life * healthBar.width, healthBar.height / 2 );
	
	if( r.hero.takingDamage && r.hero.life > 0.01 ) {
		r.particleEmitters.push( new r.ParticleEmitter( {
			x: -r.screen.x + healthBar.x + r.hero.life * healthBar.width,
			y: -r.screen.y + healthBar.y + healthBar.height / 2,
			count: 1,
			spawnRange: 2,
			friction: 0.85,
			minSpeed: 2,
			maxSpeed: 20,
			minDirection: r.pi / 2 - 0.2,
			maxDirection: r.pi / 2 + 0.2,
			hue: r.hero.life * 120,
			saturation: 100
		} ) );
	}

	/*==============================================================================
	Progress
	==============================================================================*/
	r.ctxmg.beginPath();
	var progressText = r.text( {
		ctx: r.ctxmg,
		x: healthBar.x + healthBar.width + 40,
		y: 20,
		text: 'PROGRESS',
		hspacing: 1,
		vspacing: 1,
		halign: 'top',
		valign: 'left',
		scale: 2,
		snap: 1,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
	r.ctxmg.fill();
	var progressBar = {
		x: progressText.ex + 10,
		y: progressText.sy,
		width: healthBar.width,
		height: healthBar.height
	};
	r.ctxmg.fillStyle = 'hsla(0, 0%, 20%, 1)';
	r.ctxmg.fillRect( progressBar.x, progressBar.y, progressBar.width, progressBar.height );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.25)';
	r.ctxmg.fillRect( progressBar.x, progressBar.y, progressBar.width, progressBar.height / 2 );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 50%, 1)';
	r.ctxmg.fillRect( progressBar.x, progressBar.y, ( r.level.kills / r.level.killsToLevel ) * progressBar.width, progressBar.height );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 1)';
	r.ctxmg.fillRect( progressBar.x, progressBar.y, ( r.level.kills / r.level.killsToLevel ) * progressBar.width, progressBar.height / 2 );
	
	if( r.level.kills == r.level.killsToLevel ) {
		r.particleEmitters.push( new r.ParticleEmitter( {
			x: -r.screen.x + progressBar.x + progressBar.width,
			y: -r.screen.y + progressBar.y + progressBar.height / 2,
			count: 30,
			spawnRange: 5,
			friction: 0.95,
			minSpeed: 2,
			maxSpeed: 25,
			minDirection: 0,
			minDirection: r.pi / 2 - r.pi / 4,
			maxDirection: r.pi / 2 + r.pi / 4,
			hue: 0,
			saturation: 0
		} ) );
	}

	/*==============================================================================
	Score
	==============================================================================*/
	r.ctxmg.beginPath();
	var scoreLabel = r.text( {
		ctx: r.ctxmg,
		x: progressBar.x + progressBar.width + 40,
		y: 20,
		text: 'SCORE',
		hspacing: 1,
		vspacing: 1,
		halign: 'top',
		valign: 'left',
		scale: 2,
		snap: 1,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
	r.ctxmg.fill();

	r.ctxmg.beginPath();
	var scoreText = r.text( {
		ctx: r.ctxmg,
		x: scoreLabel.ex + 10,
		y: 20,
		text: r.util.pad( r.score, 6 ),
		hspacing: 1,
		vspacing: 1,
		halign: 'top',
		valign: 'left',
		scale: 2,
		snap: 1,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 1)';
	r.ctxmg.fill();

	r.ctxmg.beginPath();
	var bestLabel = r.text( {
		ctx: r.ctxmg,
		x: scoreText.ex + 40,
		y: 20,
		text: 'BEST',
		hspacing: 1,
		vspacing: 1,
		halign: 'top',
		valign: 'left',
		scale: 2,
		snap: 1,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
	r.ctxmg.fill();

	r.ctxmg.beginPath();
	var bestText = r.text( {
		ctx: r.ctxmg,
		x: bestLabel.ex + 10,
		y: 20,
		text: r.util.pad( Math.max( r.storage['score'], r.score ), 6 ),
		hspacing: 1,
		vspacing: 1,
		halign: 'top',
		valign: 'left',
		scale: 2,
		snap: 1,
		render: 1
	} );
	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 1)';
	r.ctxmg.fill();
};

r.renderMinimap = function() {
	r.ctxmg.fillStyle = r.minimap.color;
	r.ctxmg.fillRect( r.minimap.x, r.minimap.y, r.minimap.width, r.minimap.height );

	r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.1)';
	r.ctxmg.fillRect( 
		Math.floor( r.minimap.x + -r.screen.x * r.minimap.scale ), 
		Math.floor( r.minimap.y + -r.screen.y * r.minimap.scale ), 
		Math.floor( r.cw * r.minimap.scale ), 
		Math.floor( r.ch * r.minimap.scale )
	);

	//r.ctxmg.beginPath();
	for( var i = 0; i < r.enemies.length; i++ ){
		var enemy = r.enemies[ i ],
			x = r.minimap.x + Math.floor( enemy.x * r.minimap.scale ),
			y = r.minimap.y + Math.floor( enemy.y * r.minimap.scale );
		if( r.util.pointInRect( x + 1, y + 1, r.minimap.x, r.minimap.y, r.minimap.width, r.minimap.height ) ) {
			//r.ctxmg.rect( x, y, 2, 2 );
			r.ctxmg.fillStyle = 'hsl(' + enemy.hue + ', ' + enemy.saturation + '%, 50%)';
			r.ctxmg.fillRect( x, y, 2, 2 );
		}
	}
	//r.ctxmg.fillStyle = '#f00';
	//r.ctxmg.fill();

	r.ctxmg.beginPath();
	for( var i = 0; i < r.bullets.length; i++ ){
		var bullet = r.bullets[ i ],
			x = r.minimap.x + Math.floor( bullet.x * r.minimap.scale ),
			y = r.minimap.y + Math.floor( bullet.y * r.minimap.scale );
		if( r.util.pointInRect( x, y, r.minimap.x, r.minimap.y, r.minimap.width, r.minimap.height ) ) {
			r.ctxmg.rect( x, y, 1, 1 );
		}
	}
	r.ctxmg.fillStyle = '#fff';
	r.ctxmg.fill();

	r.ctxmg.fillStyle = r.hero.fillStyle;
	r.ctxmg.fillRect( r.minimap.x + Math.floor( r.hero.x * r.minimap.scale ), r.minimap.y + Math.floor( r.hero.y * r.minimap.scale ), 2, 2 );

	r.ctxmg.strokeStyle = r.minimap.strokeColor;
	r.ctxmg.strokeRect( r.minimap.x - 0.5, r.minimap.y - 0.5, r.minimap.width + 1, r.minimap.height + 1 );
};

/*==============================================================================
Enemy Spawning
==============================================================================*/
r.getSpawnCoordinates = function( radius ) {
	var quadrant = Math.floor( r.util.rand( 0, 4 ) ),
		x,
		y,
		start;
	
	if( quadrant === 0){
		x = r.util.rand( 0, r.ww );
		y = -radius;
		start = 'top';
	} else if( quadrant === 1 ){
		x = r.ww + radius;
		y = r.util.rand( 0, r.wh );
		start = 'right';
	} else if( quadrant === 2 ) {
		x = r.util.rand( 0, r.ww );
		y = r.wh + radius;
		start = 'bottom';
	} else {
		x = -radius;
		y = r.util.rand( 0, r.wh );
		start = 'left';
	}

	return { x: x, y: y, start: start };
};

r.spawnEnemy = function( type ) {
	var params = r.definitions.enemies[ type ],
		coordinates = r.getSpawnCoordinates( params.radius );
	params.x = coordinates.x;
	params.y = coordinates.y;
	params.start = coordinates.start;
	params.type = type;
	return new r.Enemy( params );
};

r.spawnEnemies = function() {
	var floorTick = Math.floor( r.tick );
	for( var i = 0; i < r.level.distributionCount; i++ ) {
		var timeCheck = r.level.distribution[ i ];		
		if( r.levelDiffOffset > 0 ){
			timeCheck = Math.max( 1, timeCheck - ( r.levelDiffOffset * 2) );
		}
		if( floorTick % timeCheck === 0 ) {
			r.enemies.push( r.spawnEnemy( i ) );
		}
	}
};

/*==============================================================================
Events
==============================================================================*/
r.mousemovecb = function( e ) {
	e.preventDefault();
	r.mouse.ax = e.pageX;
	r.mouse.ay = e.pageY;
	r.mousescreen();
};

r.mousescreen = function() {
	r.mouse.sx = r.mouse.ax - r.cOffset.left;
	r.mouse.sy = r.mouse.ay - r.cOffset.top;
	r.mouse.x = r.mouse.sx - r.screen.x;
	r.mouse.y = r.mouse.sy - r.screen.y;
};

r.mousedowncb = function( e ) {
	e.preventDefault();
	r.mouse.down = 1;
};

r.mouseupcb = function( e ) {
	e.preventDefault();
	r.mouse.down = 0;
};

r.keydowncb = function( e ) {
	var e = ( e.keyCode ? e.keyCode : e.which );
	if( e === 38 || e === 87 ){ r.keys.state.up = 1; }
	if( e === 39 || e === 68 ){ r.keys.state.right = 1; }
	if( e === 40 || e === 83 ){ r.keys.state.down = 1; }
	if( e === 37 || e === 65 ){ r.keys.state.left = 1; }
	if( e === 70 ){ r.keys.state.f = 1; }
	if( e === 77 ){ r.keys.state.m = 1; }
	if( e === 80 ){ r.keys.state.p = 1; }
}

r.keyupcb = function( e ) {
	var e = ( e.keyCode ? e.keyCode : e.which );
	if( e === 38 || e === 87 ){ r.keys.state.up = 0; }
	if( e === 39 || e === 68 ){ r.keys.state.right = 0; }
	if( e === 40 || e === 83 ){ r.keys.state.down = 0; }
	if( e === 37 || e === 65 ){ r.keys.state.left = 0; }
	if( e === 70 ){ r.keys.state.f = 0; }
	if( e === 77 ){ r.keys.state.m = 0; }
	if( e === 80 ){ r.keys.state.p = 0; }
}

r.resizecb = function( e ) {
	var rect = r.cmg.getBoundingClientRect();
	r.cOffset = {
		left: rect.left,
		top: rect.top
	}
}

r.blurcb = function() {
	if( r.state == 'play' ){
		r.setState( 'pause' );
	}
}

r.bindEvents = function() {
	window.addEventListener( 'mousemove', r.mousemovecb );
	window.addEventListener( 'mousedown', r.mousedowncb );
	window.addEventListener( 'mouseup', r.mouseupcb );
	window.addEventListener( 'keydown', r.keydowncb );
	window.addEventListener( 'keyup', r.keyupcb );
	window.addEventListener( 'resize', r.resizecb );
	window.addEventListener( 'blur', r.blurcb );
};

/*==============================================================================
Miscellaneous
==============================================================================*/
r.clearScreen = function() {
	r.ctxmg.clearRect( 0, 0, r.cw, r.ch );
};

r.updateDelta = function() { 
	var now = Date.now();
	r.dt = ( now - r.lt ) / ( 1000 / 60 );
	r.dt = ( r.dt < 0 ) ? 0.001 : r.dt;
	r.dt = ( r.dt > 10 ) ? 10 : r.dt;
	r.lt = now;
	r.elapsed += r.dt;
};

r.updateScreen = function() {
	var xSnap,
		xModify, 
		ySnap,
		yModify;

	if( r.hero.x < r.cw / 2 ) {
		xModify = r.hero.x / r.cw;
	} else if( r.hero.x > r.ww - r.cw / 2 ) {
		xModify = 1 - ( r.ww - r.hero.x ) / r.cw;
	} else {
		xModify = 0.5;		
	}

	if( r.hero.y < r.ch / 2 ) {
		yModify = r.hero.y / r.ch;
	} else if( r.hero.y > r.wh - r.ch / 2 ) {
		yModify = 1 - ( r.wh - r.hero.y ) / r.ch;
	} else {
		yModify = 0.5;		
	}

	xSnap = ( ( r.cw * xModify - r.hero.x ) - r.screen.x ) / 30;
	ySnap = ( ( r.ch * yModify - r.hero.y ) - r.screen.y ) / 30;	

	// ease to new coordinates
	r.screen.x += xSnap * r.dt;
	r.screen.y += ySnap * r.dt;

	// update rumble levels, keep X and Y changes consistent, apply rumble
	if( r.rumble.level > 0 ) {
		r.rumble.level -= r.rumble.decay;
		r.rumble.level = ( r.rumble.level < 0 ) ? 0 : r.rumble.level;			
		r.rumble.x = r.util.rand( -r.rumble.level, r.rumble.level );
		r.rumble.y = r.util.rand( -r.rumble.level, r.rumble.level );
	} else {
		r.rumble.x = 0;
		r.rumble.y = 0;
	}

	//r.screen.x -= r.rumble.x;
	//r.screen.y -= r.rumble.y;

	// animate background canvas
	r.cbg1.style.marginLeft = 
		-( ( r.cbg1.width - r.cw ) / 2 ) // half the difference from bg to viewport
		- ( ( r.cbg1.width - r.cw ) / 2 ) // half the diff again, modified by a percentage below
		* ( ( -r.screen.x - ( r.ww - r.cw ) / 2 ) / ( ( r.ww - r.cw ) / 2) ) // viewport offset applied to bg
		- r.rumble.x + 'px';
	r.cbg1.style.marginTop = 
		-( ( r.cbg1.height - r.ch ) / 2 ) 
		- ( ( r.cbg1.height - r.ch ) / 2 )
		* ( ( -r.screen.y - ( r.wh - r.ch ) / 2 ) / ( ( r.wh - r.ch ) / 2) ) 
		- r.rumble.y + 'px';
	r.cbg2.style.marginLeft = 
		-( ( r.cbg2.width - r.cw ) / 2 ) // half the difference from bg to viewport
		- ( ( r.cbg2.width - r.cw ) / 2 ) // half the diff again, modified by a percentage below
		* ( ( -r.screen.x - ( r.ww - r.cw ) / 2 ) / ( ( r.ww - r.cw ) / 2) ) // viewport offset applied to bg
		- r.rumble.x + 'px';
	r.cbg2.style.marginTop = 
		-( ( r.cbg2.height - r.ch ) / 2 ) 
		- ( ( r.cbg2.height - r.ch ) / 2 )
		* ( ( -r.screen.y - ( r.wh - r.ch ) / 2 ) / ( ( r.wh - r.ch ) / 2) ) 
		- r.rumble.y + 'px';
	r.cbg3.style.marginLeft = 
		-( ( r.cbg3.width - r.cw ) / 2 ) // half the difference from bg to viewport
		- ( ( r.cbg3.width - r.cw ) / 2 ) // half the diff again, modified by a percentage below
		* ( ( -r.screen.x - ( r.ww - r.cw ) / 2 ) / ( ( r.ww - r.cw ) / 2) ) // viewport offset applied to bg
		- r.rumble.x + 'px';
	r.cbg3.style.marginTop = 
		-( ( r.cbg3.height - r.ch ) / 2 ) 
		- ( ( r.cbg3.height - r.ch ) / 2 )
		* ( ( -r.screen.y - ( r.wh - r.ch ) / 2 ) / ( ( r.wh - r.ch ) / 2) ) 
		- r.rumble.y + 'px';
	r.cbg4.style.marginLeft = 
		-( ( r.cbg4.width - r.cw ) / 2 ) // half the difference from bg to viewport
		- ( ( r.cbg4.width - r.cw ) / 2 ) // half the diff again, modified by a percentage below
		* ( ( -r.screen.x - ( r.ww - r.cw ) / 2 ) / ( ( r.ww - r.cw ) / 2) ) // viewport offset applied to bg
		- r.rumble.x + 'px';
	r.cbg4.style.marginTop = 
		-( ( r.cbg4.height - r.ch ) / 2 ) 
		- ( ( r.cbg4.height - r.ch ) / 2 )
		* ( ( -r.screen.y - ( r.wh - r.ch ) / 2 ) / ( ( r.wh - r.ch ) / 2) ) 
		- r.rumble.y + 'px';

	r.mousescreen();
};

r.updateLevel = function() {
	if( r.level.kills >= r.level.killsToLevel ) {
		if( r.level.current + 1 < r.levelCount ){
			r.level.current++;
			r.level.kills = 0;
			r.level.killsToLevel = r.definitions.levels[ r.level.current ].killsToLevel;
			r.level.distribution = r.definitions.levels[ r.level.current ].distribution;
			r.level.distributionCount = r.level.distribution.length;
		} else {
			r.level.current++;
			r.level.kills = 0;
			// no more level definitions, so take the last level and increase the spawn rate slightly
			//for( var i = 0; i < r.level.distributionCount; i++ ) {
				//r.level.distribution[ i ] = Math.max( 1, r.level.distribution[ i ] - 5 );
			//}
		}
		r.levelDiffOffset = r.level.current + 1 - r.levelCount;
		r.levelPops.push( new r.LevelPop( {
			level: r.level.current + 1
		} ) );
	}
};

r.updatePowerupTimers = function() {
	// HEALTH
	if( r.powerupTimers[ 0 ] > 0 ){
		if( r.hero.life < 1 ) {
			r.hero.life += 0.001;
		}
		if( r.hero.life > 1 ) {
			r.hero.life = 1;
		}
		r.powerupTimers[ 0 ] -= r.dt;
	}

	// SLOW ENEMIES
	if( r.powerupTimers[ 1 ] > 0 ){
		r.slow = 1;
		r.powerupTimers[ 1 ] -= r.dt;
	} else {
		r.slow = 0;
	}

	// FAST SHOT
	if( r.powerupTimers[ 2 ] > 0 ){
		r.hero.weapon.fireRate = 2;
		r.hero.weapon.bullet.speed = 14;
		r.powerupTimers[ 2 ] -= r.dt;
	} else {
		r.hero.weapon.fireRate = 5;
		r.hero.weapon.bullet.speed = 10;
	}

	// TRIPLE SHOT
	if( r.powerupTimers[ 3 ] > 0 ){
		r.hero.weapon.count = 3;
		r.powerupTimers[ 3 ] -= r.dt;
	} else {
		r.hero.weapon.count = 1;
	}

	// PIERCE SHOT
	if( r.powerupTimers[ 4 ] > 0 ){
		r.hero.weapon.bullet.piercing = 1;
		r.powerupTimers[ 4 ] -= r.dt;
	} else {
		r.hero.weapon.bullet.piercing = 0;
	}
};	

r.spawnPowerup = function( x, y ) {
	if( Math.random() < 0.1 ) {
		var min = ( r.hero.life < 0.9 ) ? 0 : 1,
			type = Math.floor( r.util.rand( min, r.definitions.powerups.length ) ),
			params = r.definitions.powerups[ type ];
		params.type = type;
		params.x = x;
		params.y = y;
		r.powerups.push( new r.Powerup( params ) );
	}
};

/*==============================================================================
States
==============================================================================*/
r.setState = function( state ) {
	// handle clean up between states
	r.buttons.length = 0;

	if( state == 'menu' ) {
		r.mouse.down = 0;		
		r.mouse.ax = 0;
		r.mouse.ay = 0;

		r.reset();

		var playButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: r.ch / 2 - 24,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'PLAY',
			action: function() {
				r.reset();
				r.audio.play( 'levelup' );
				r.setState( 'play' );
			}
		} );
		r.buttons.push( playButton );

		var statsButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: playButton.ey + 25,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'STATS',
			action: function() {
				r.setState( 'stats' );
			}
		} );
		r.buttons.push( statsButton );

		var creditsButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: statsButton.ey + 26,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'CREDITS',
			action: function() {
				r.setState( 'credits' );
			}
		} ) ;
		r.buttons.push( creditsButton );
	}

	if( state == 'stats' ) {
		r.mouse.down = 0;
	
		var clearButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: 426,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'CLEAR DATA',
			action: function() {
				r.mouse.down = 0;				
				if( window.confirm( 'Are you sure you want to clear all locally stored game data? This cannot be undone.') ) {
					r.clearStorage();
					r.mouse.down = 0;
				}
			}
		} );
		r.buttons.push( clearButton );

		var menuButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: clearButton.ey + 25,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'MENU',
			action: function() {
				r.setState( 'menu' );
			}
		} );
		r.buttons.push( menuButton );	
	}

	if( state == 'credits' ) {
		r.mouse.down = 0;

		var js13kButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: 476,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'JS13KGAMES',
			action: function() {				
				location.href = 'http://js13kgames.com';
				r.mouse.down = 0;
			}
		} );
		r.buttons.push( js13kButton );

		var menuButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: js13kButton.ey + 25,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'MENU',
			action: function() {
				r.setState( 'menu' );
			}
		} );
		r.buttons.push( menuButton );	
	}

	if( state == 'pause' ) {
		r.mouse.down = 0;
		r.screenshot = r.ctxmg.getImageData( 0, 0, r.cw, r.ch );
		var resumeButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: r.ch / 2 + 26,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'RESUME',
			action: function() {
				r.lt = Date.now() + 1000;
				r.setState( 'play' );
			}
		} );
		r.buttons.push( resumeButton );

		var menuButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: resumeButton.ey + 25,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'MENU',
			action: function() {
				r.mouse.down = 0;
				if( window.confirm( 'Are you sure you want to end this game and return to the menu?') ) {
					r.mousescreen();
					r.setState( 'menu' );
				}			
			}
		} );
		r.buttons.push( menuButton );
	}

	if( state == 'gameover' ) {
		r.mouse.down = 0;
	
		r.screenshot = r.ctxmg.getImageData( 0, 0, r.cw, r.ch );
		var resumeButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: 426,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'PLAY AGAIN',
			action: function() {
				r.reset();
				r.audio.play( 'levelup' );
				r.setState( 'play' );
			}
		} );
		r.buttons.push( resumeButton );

		var menuButton = new r.Button( {
			x: r.cw / 2 + 1,
			y: resumeButton.ey + 25,
			lockedWidth: 299,
			lockedHeight: 49,
			scale: 3,
			title: 'MENU',
			action: function() {
				r.setState( 'menu' );
			}
		} );
		r.buttons.push( menuButton );

		r.storage['score'] = Math.max( r.storage['score'], r.score );
		r.storage['level'] = Math.max( r.storage['level'], r.level.current );		
		r.storage['rounds'] += 1;
		r.storage['kills'] += r.kills;
		r.storage['bullets'] += r.bulletsFired;
		r.storage['powerups'] += r.powerupsCollected;		
		r.storage['time'] += Math.floor( r.elapsed );
		r.updateStorage();
	}

	// set state
	r.state = state;
};

r.setupStates = function() {
	r.states['menu'] = function() {
		r.clearScreen();
		r.updateScreen();

		var i = r.buttons.length; while( i-- ){ r.buttons[ i ].update( i ) }
			i = r.buttons.length; while( i-- ){ r.buttons[ i ].render( i ) }

		r.ctxmg.beginPath();
		var title = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2,
			y: r.ch / 2 - 100,
			text: 'RADIUS RAID',
			hspacing: 2,
			vspacing: 1,
			halign: 'center',
			valign: 'bottom',
			scale: 10,
			snap: 1,
			render: 1
		} );
		gradient = r.ctxmg.createLinearGradient( title.sx, title.sy, title.sx, title.ey );
		gradient.addColorStop( 0, '#fff' );
		gradient.addColorStop( 1, '#999' );
		r.ctxmg.fillStyle = gradient;
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var bottomInfo = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2,
			y: r.ch - 172,
			text: 'CREATED BY JACK RUGILE FOR JS13KGAMES 2013',
			hspacing: 1,
			vspacing: 1,
			halign: 'center',
			valign: 'bottom',
			scale: 1,
			snap: 1,
			render: 1
		} );
		r.ctxmg.fillStyle = '#666';
		r.ctxmg.fill();

	};

	r.states['stats'] = function() {
		r.clearScreen();

		r.ctxmg.beginPath();
		var statsTitle = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2,
			y: 150,
			text: 'STATS',
			hspacing: 3,
			vspacing: 1,
			halign: 'center',
			valign: 'bottom',
			scale: 10,
			snap: 1,
			render: 1
		} );
		var gradient = r.ctxmg.createLinearGradient( statsTitle.sx, statsTitle.sy, statsTitle.sx, statsTitle.ey );
		gradient.addColorStop( 0, '#fff' );
		gradient.addColorStop( 1, '#999' );
		r.ctxmg.fillStyle = gradient;
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var statKeys = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2 - 10,
			y: statsTitle.ey + 39,
			text: 'BEST SCORE\nBEST LEVEL\nROUNDS PLAYED\nENEMIES KILLED\nBULLETS FIRED\nPOWERUPS COLLECTED\nTIME ELAPSED',
			hspacing: 1,
			vspacing: 17,
			halign: 'right',
			valign: 'top',
			scale: 2,
			snap: 1,
			render: 1
		} );		
		r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var statsValues = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2 + 10,
			y: statsTitle.ey + 39,
			text: 
				r.util.commas( r.storage['score'] ) + '\n' + 
				( r.storage['level'] + 1 ) + '\n' + 
				r.util.commas( r.storage['rounds'] ) + '\n' + 
				r.util.commas( r.storage['kills'] ) + '\n' + 
				r.util.commas( r.storage['bullets'] ) + '\n' + 
				r.util.commas( r.storage['powerups'] ) + '\n' + 
				r.util.convertTime( ( r.storage['time'] * ( 1000 / 60 ) ) / 1000 )
			,
			hspacing: 1,
			vspacing: 17,
			halign: 'left',
			valign: 'top',
			scale: 2,
			snap: 1,
			render: 1
		} );		
		r.ctxmg.fillStyle = '#fff';
		r.ctxmg.fill();

		var i = r.buttons.length; while( i-- ){ r.buttons[ i ].render( i ) }
			i = r.buttons.length; while( i-- ){ r.buttons[ i ].update( i ) }
	};

	r.states['credits'] = function() {
		r.clearScreen();

		r.ctxmg.beginPath();
		var creditsTitle = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2,
			y: 100,
			text: 'CREDITS',
			hspacing: 3,
			vspacing: 1,
			halign: 'center',
			valign: 'bottom',
			scale: 10,
			snap: 1,
			render: 1
		} );
		var gradient = r.ctxmg.createLinearGradient( creditsTitle.sx, creditsTitle.sy, creditsTitle.sx, creditsTitle.ey );
		gradient.addColorStop( 0, '#fff' );
		gradient.addColorStop( 1, '#999' );
		r.ctxmg.fillStyle = gradient;
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var creditKeys = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2 - 10,
			y: creditsTitle.ey + 49,
			text: 'CREATED FOR JS13KGAMES BY\nINSPIRATION AND SUPPORT\n\nAUDIO PROCESSING\nGAME INSPIRATION AND IDEAS\n\nHTML5 CANVAS REFERENCE\n\nGAME MATH REFERENCE',
			hspacing: 1,
			vspacing: 17,
			halign: 'right',
			valign: 'top',
			scale: 2,
			snap: 1,
			render: 1
		} );		
		r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var creditValues = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2 + 10,
			y: creditsTitle.ey + 49,
			text: '@JACKRUGILE\n@REZONER, @LOKTAR00, @END3R,\n@AUSTINHALLOCK, @CHANDLERPRALL\nJSFXR BY @MARKUSNEUBRAND\nASTEROIDS, CELL WARFARE,\nSPACE PIPS, AND MANY MORE\nNIHILOGIC HTML5\nCANVAS CHEAT SHEET\nBILLY LAMBERTA FOUNDATION\nHTML5 ANIMATION WITH JAVASCRIPT',
			hspacing: 1,
			vspacing: 17,
			halign: 'left',
			valign: 'top',
			scale: 2,
			snap: 1,
			render: 1
		} );		
		r.ctxmg.fillStyle = '#fff';
		r.ctxmg.fill();

		var i = r.buttons.length; while( i-- ){ r.buttons[ i ].render( i ) }
			i = r.buttons.length; while( i-- ){ r.buttons[ i ].update( i ) }
	};

	r.states['play'] = function() {
		r.updateDelta();
		r.updateScreen();
		r.updateLevel();
		r.updatePowerupTimers();
		r.spawnEnemies();
		r.enemyOffsetMod += ( r.slow ) ? r.dt / 3 : r.dt;
		
		// update entities	
		var i = r.enemies.length; while( i-- ){ r.enemies[ i ].update( i ) }
			i = r.explosions.length; while( i-- ){ r.explosions[ i ].update( i ) }
			i = r.powerups.length; while( i-- ){ r.powerups[ i ].update( i ) }
			i = r.particleEmitters.length; while( i-- ){ r.particleEmitters[ i ].update( i ) }
			i = r.textPops.length; while( i-- ){ r.textPops[ i ].update( i ) }
			i = r.levelPops.length; while( i-- ){ r.levelPops[ i ].update( i ) }
			i = r.bullets.length; while( i-- ){ r.bullets[ i ].update( i ) }
		r.hero.update();

		// render entities
		r.clearScreen();
		r.ctxmg.save();
		r.ctxmg.translate( r.screen.x - r.rumble.x, r.screen.y - r.rumble.y );
		i = r.enemies.length; while( i-- ){ r.enemies[ i ].render( i ) }
		i = r.explosions.length; while( i-- ){ r.explosions[ i ].render( i ) }
		i = r.powerups.length; while( i-- ){ r.powerups[ i ].render( i ) }
		i = r.particleEmitters.length; while( i-- ){ r.particleEmitters[ i ].render( i ) }
		i = r.textPops.length; while( i-- ){ r.textPops[ i ].render( i ) }		
		i = r.bullets.length; while( i-- ){ r.bullets[ i ].render( i ) }
		r.hero.render();		
		r.ctxmg.restore();		
		i = r.levelPops.length; while( i-- ){ r.levelPops[ i ].render( i ) }
		r.renderInterface();
		r.renderMinimap();

		// handle gameover
		if( r.hero.life <= 0 ) {
			var alpha = ( ( r.gameoverTick / r.gameoverTickMax ) * 0.8 );
				alpha = Math.min( 1, Math.max( 0, alpha ) );
			r.ctxmg.fillStyle = 'hsla(0, 100%, 0%, ' + alpha + ')';
			r.ctxmg.fillRect( 0, 0, r.cw, r.ch );
			if( r.gameoverTick < r.gameoverTickMax ){				
				r.gameoverTick += r.dt;				
			} else {
				r.setState( 'gameover' );
			}

			if( !r.gameoverExplosion ) {
				r.audio.play( 'death' );
				r.rumble.level = 25;
				r.explosions.push( new r.Explosion( {
					x: r.hero.x + r.util.rand( -10, 10 ),
					y: r.hero.y + r.util.rand( -10, 10 ),
					radius: 50,
					hue: 0,
					saturation: 0
				} ) );
				r.particleEmitters.push( new r.ParticleEmitter( {
					x: r.hero.x,
					y: r.hero.y,
					count: 45,
					spawnRange: 10,
					friction: 0.95,
					minSpeed: 2,
					maxSpeed: 20,
					minDirection: 0,
					maxDirection: r.twopi,
					hue: 0,
					saturation: 0
				} ) );
				for( var i = 0; i < r.powerupTimers.length; i++ ){
					r.powerupTimers[ i ] = 0;
				}
				r.gameoverExplosion = 1;
			}		
		}

		// update tick	
		r.tick += r.dt;	

		// listen for pause
		if( r.keys.pressed.p ){
			r.setState( 'pause' );
		}

		// always listen for autofire toggle
		if( r.keys.pressed.f ){
			r.autofire = ~~!r.autofire;			
			r.storage['autofire'] = r.autofire;
			r.updateStorage();
		}
	};

	r.states['pause'] = function() {
		r.clearScreen();
		r.ctxmg.putImageData( r.screenshot, 0, 0 );

		r.ctxmg.fillStyle = 'hsla(0, 0%, 0%, 0.4)';
		r.ctxmg.fillRect( 0, 0, r.cw, r.ch );

		r.ctxmg.beginPath();
		var pauseText = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2,
			y: r.ch / 2 - 50,
			text: 'PAUSED',
			hspacing: 3,
			vspacing: 1,
			halign: 'center',
			valign: 'bottom',
			scale: 10,
			snap: 1,
			render: 1
		} );
		var gradient = r.ctxmg.createLinearGradient( pauseText.sx, pauseText.sy, pauseText.sx, pauseText.ey );
		gradient.addColorStop( 0, '#fff' );
		gradient.addColorStop( 1, '#999' );
		r.ctxmg.fillStyle = gradient;
		r.ctxmg.fill();

		var i = r.buttons.length; while( i-- ){ r.buttons[ i ].render( i ) }
			i = r.buttons.length; while( i-- ){ r.buttons[ i ].update( i ) }

		if( r.keys.pressed.p ){
			r.setState( 'play' );
		}
	};

	r.states['gameover'] = function() {
		r.clearScreen();
		r.ctxmg.putImageData( r.screenshot, 0, 0 );

		var i = r.buttons.length; while( i-- ){ r.buttons[ i ].update( i ) }
			i = r.buttons.length; while( i-- ){ r.buttons[ i ].render( i ) }

		r.ctxmg.beginPath();
		var gameoverTitle = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2,
			y: 150,
			text: 'GAME OVER',
			hspacing: 3,
			vspacing: 1,
			halign: 'center',
			valign: 'bottom',
			scale: 10,
			snap: 1,
			render: 1
		} );
		var gradient = r.ctxmg.createLinearGradient( gameoverTitle.sx, gameoverTitle.sy, gameoverTitle.sx, gameoverTitle.ey );
		gradient.addColorStop( 0, '#f22' );
		gradient.addColorStop( 1, '#b00' );
		r.ctxmg.fillStyle = gradient;
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var gameoverStatsKeys = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2 - 10,
			y: gameoverTitle.ey + 51,
			text: 'SCORE\nLEVEL\nKILLS\nBULLETS\nPOWERUPS\nTIME',
			hspacing: 1,
			vspacing: 17,
			halign: 'right',
			valign: 'top',
			scale: 2,
			snap: 1,
			render: 1
		} );		
		r.ctxmg.fillStyle = 'hsla(0, 0%, 100%, 0.5)';
		r.ctxmg.fill();

		r.ctxmg.beginPath();
		var gameoverStatsValues = r.text( {
			ctx: r.ctxmg,
			x: r.cw / 2 + 10,
			y: gameoverTitle.ey + 51,
			text: 
				r.util.commas( r.score ) + '\n' + 
				( r.level.current + 1 ) + '\n' + 
				r.util.commas( r.kills ) + '\n' + 
				r.util.commas( r.bulletsFired ) + '\n' + 
				r.util.commas( r.powerupsCollected ) + '\n' + 
				r.util.convertTime( ( r.elapsed * ( 1000 / 60 ) ) / 1000 )
			,
			hspacing: 1,
			vspacing: 17,
			halign: 'left',
			valign: 'top',
			scale: 2,
			snap: 1,
			render: 1
		} );		
		r.ctxmg.fillStyle = '#fff';
		r.ctxmg.fill();
	};
}

/*==============================================================================
Loop
==============================================================================*/
r.loop = function() {
	requestAnimFrame( r.loop );

	// setup the pressed state for all keys
	for( var k in r.keys.state ) {
		if( r.keys.state[ k ] && !r.okeys[ k ] ) {
			r.keys.pressed[ k ] = 1;
		} else {
			r.keys.pressed[ k ] = 0;
		}
	}

	// run the current state
	r.states[ r.state ]();

	// always listen for mute toggle
	if( r.keys.pressed.m ){
		r.mute = ~~!r.mute;
		var i = r.audio.references.length;
		while( i-- ) {
			r.audio.references[ i ].volume = ~~!r.mute;
		}
		r.storage['mute'] = r.mute;
		r.updateStorage();
	}

	// move current keys into old keys
	r.okeys = {};
	for( var k in r.keys.state ) {
		r.okeys[ k ] = r.keys.state[ k ];
	}
};

/*==============================================================================
Start Game on Load
==============================================================================*/
window.addEventListener( 'load', function() {
	document.documentElement.className += ' loaded';
	r.init();
});

