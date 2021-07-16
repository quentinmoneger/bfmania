<!-- Google Icons (Material Design) -->
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" crossorigin="anonymous">

<!-- Chessboard JS -->
<link rel="stylesheet" href="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.css" integrity="sha384-q94+BZtLrkL1/ohfjR8c6L+A6qzNH9R2hBLwyoAfu3i/WCvQjzL2RQJ3uNHDISdU" crossorigin="anonymous">
<script defer src="/assets/js/front/game/assets/chees/lib.js"></script>


<!-- Bootstrap JS -->
<script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous">
</script>

<!-- Custom JS -->
<script defer src="/assets/js/front/game/assets/chees/main.js"></script>

<div class="container my-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="text-align-center">Simple Chess AI</h1>
            <div class="row justify-content-center">
                <span id="position-count">0</span> &nbsp positions evaluated in &nbsp <span id="time">0</span>s.
            </div>
            <div class="row mb-3 justify-content-center">
                That's &nbsp <span id="positions-per-s">0</span> &nbsp positions / s.
            </div>
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="settingsHeading">
                        <h2 class="text-align-center">
                            <button class="btn btn-header no-outline" data-toggle="collapse" data-target="#settings" aria-expanded="true" aria-controls="settings">
                                Settings
                            </button>
                        </h2>
                    </div>
                </div>
                <div id="settings" class="collapse" aria-labelledby="settingsHeading" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row align-items-center justify-content-center">
                            <div class="form-group">
                                <label for="search-depth">Search Depth (Black):</label>
                                <select id="search-depth">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3" selected>3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-center justify-content-center">
                            <div class="form-group">
                                <label for="search-depth-white">Search Depth (White):</label>
                                <select id="search-depth-white">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3" selected>3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-center justify-content-center">
                            <div class="form-group">
                                <input type="checkbox" id="showHint" name="showHint" value="showHint">
                                <label for="showHint">Show Suggested Move (White)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="openingPositionsHeading">
                        <h2 class="text-align-center">
                            <button class="btn btn-header no-outline" data-toggle="collapse" data-target="#openingPositions" aria-expanded="true" aria-controls="openingPositions">
                                Opening Positions
                            </button>
                        </h2>
                    </div>
                </div>
                <div id="openingPositions" class="collapse" aria-labelledby="openingPositionsHeading" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row my-3 text-align-center">
                            <div class="col-md-6 my-2">
                                <button class="btn btn-primary" id="ruyLopezBtn">Ruy Lopez</button>
                            </div>
                            <div class="col-md-6 my-2">
                                <button class="btn btn-primary" id="italianGameBtn">Italian Game</button>
                            </div>
                        </div>
                        <div class="row my-3 text-align-center">
                            <div class="col-md-6 my-2">
                                <button class="btn btn-primary" id="sicilianDefenseBtn">Sicilian Defense</button>
                            </div>
                            <div class="col-md-6 my-2">
                                <button class="btn btn-primary" id="startBtn">Start Position</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="compVsCompHeading">
                        <h2 class="text-align-center">
                            <button class="btn btn-header no-outline" data-toggle="collapse" data-target="#compVsComp" aria-expanded="true" aria-controls="compVsComp">
                                Computer vs. Computer
                            </button>
                        </h2>
                    </div>
                </div>
            </div>
            <div id="compVsComp" class="collapse" aria-labelledby="compVsCompHeading" data-parent="#accordion">
                <div class="card-body">
                    <div class="row text-align-center">
                        <div class="col-md-6 my-2">
                            <button class="btn btn-success" id="compVsCompBtn">Start Game</button>
                        </div>
                        <div class="col-md-6 my-2">
                            <button class="btn btn-danger" id="resetBtn">Stop and Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row my-3 text-align-center">
                <div class="col-md-12">
                    <h2>Advantage</h2>
                    <p><span id="advantageColor">Neither side</span> has the advantage
                        (+<span id="advantageNumber">0</span>).</p>
                    <div class="progress">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" style="width: 50%" aria-valuemin="-2000" aria-valuemax="4000" id='advantageBar'>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row my-3 text-align-center">
                <div class="col-md-12">
                    <h2>Status</h2>
                    <p><span id="status">No check, checkmate, or draw.</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="myBoard"></div>
            <div class="row my-3 text-align-center">
                <div class="col-md-6 my-2">
                    <button class="btn btn-danger" id="undoBtn">Undo</button>
                </div>
                <div class="col-md-6 my-2">
                    <button class="btn btn-success" id="redoBtn">Redo</button>
                </div>
            </div>
        </div>
    </div>
</div>