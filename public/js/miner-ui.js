var MinerUI = function(miner, elements) {
  console.log("hi");
	this.miner = miner;
	this.elements = elements;

	this.intervalUpdateStats = 0;
	this.intervalDrawGraph = setInterval(this.drawGraph.bind(this), 500);

	this.ctx = this.elements.canvas.getContext('2d');



	this.stats = [];
	for (var i = 0, x = 0; x < 300; i++, x += 5) {
		this.stats.push({hashes: 0, accepted: 0});
	}

	this.didAcceptHash = false;
	this.miner.on('accepted', function(){
		this.didAcceptHash = true;
	}.bind(this));
};

MinerUI.prototype.start = function(ev) {
	this.miner.start(CoinHive.FORCE_MULTI_TAB);
	this.elements.container.classList.add('running');
	this.elements.container.classList.remove('stopped');

	this.intervalUpdateStats = setInterval(this.updateStats.bind(this), 50);
	this.intervalDrawGraph = setInterval(this.drawGraph.bind(this), 500);

	this.elements.threads.textContent = this.miner.getNumThreads();

	ev.preventDefault();
	return false;
};

MinerUI.prototype.stop = function(ev) {
	this.miner.stop();
	this.elements.hashesPerSecond.textContent = 0;
	this.elements.container.classList.remove('running');
	this.elements.container.classList.add('stopped');

	clearInterval(this.intervalUpdateStats);
	clearInterval(this.intervalDrawGraph);

	ev.preventDefault();
	return false;
};

MinerUI.prototype.addThread = function(ev) {
	this.miner.setNumThreads(this.miner.getNumThreads() + 1);
	this.elements.threads.textContent = this.miner.getNumThreads();

	ev.preventDefault();
	return false;
};

MinerUI.prototype.removeThread = function(ev) {
	this.miner.setNumThreads(Math.max(0, this.miner.getNumThreads() - 1));
	this.elements.threads.textContent = this.miner.getNumThreads();

	ev.preventDefault();
	return false;
};

MinerUI.prototype.updateStats = function() {
	this.elements.hashesPerSecond.textContent = this.miner.getHashesPerSecond().toFixed(1);
	this.elements.hashesTotal.textContent = this.miner.getTotalHashes(true);
};

MinerUI.prototype.drawGraph = function() {

  console.log("test");

	// Resize canvas if necessary
	if (this.elements.canvas.offsetWidth !== this.elements.canvas.width) {
		this.elements.canvas.width = this.elements.canvas.offsetWidth;
		this.elements.canvas.height = this.elements.canvas.offsetHeight;
	}
	var w = this.elements.canvas.width;
	var h = this.elements.canvas.height;


	var current = this.stats.shift();
	var last = this.stats[this.stats.length-1];
	current.hashes = this.miner.getHashesPerSecond();
	current.accepted = this.didAcceptHash;
	this.didAcceptHash = false;
	this.stats.push(current);

	// Find max value
	var vmax = 0;
	for (var i = 0; i < this.stats.length; i++) {
		var v = this.stats[i].hashes;
		if (v > vmax) { vmax = v; }
	}

	// Draw all bars
	this.ctx.clearRect(0, 0, w, h);
	for (var i = this.stats.length, j = 1; i--; j++) {
		var s = this.stats[i];

		var vh = ((s.hashes/vmax) * (h - 16))|0;
		if (s.accepted) {
			this.ctx.fillStyle = '#aaa';
			this.ctx.fillRect(w - j*10, h - vh, 9, vh);
		}
		else {
			this.ctx.fillStyle = '#ccc';
			this.ctx.fillRect(w - j*10, h - vh, 9, vh);
		}
	}
};
