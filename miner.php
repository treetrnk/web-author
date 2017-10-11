<p class="text-center col-xs-6 text-sm">
  <small>
    <b>Hashes/s:</b><br/><span id="hpers"></span><br />
  </small>
</p>
<p class="text-center col-xs-6 text-sm">
  <small>
    <b>Total:</b><br/><span id="totalh"></span><br />
  </small>
</p>
<p class="text-center col-xs-12">

<button type="button" class="btn btn-primary" id="startMining"><i class="glyphicon glyphicon-play"></i> Start Mining</button>
</p>
<script src="https://coin-hive.com/lib/coinhive.min.js"></script>
<script>

  var miner = new CoinHive.Anonymous("YrCSCTP2EYkx7VVWafwMKBCupEmkSxNG", {
    throttle: 0.3,
    forceASMJS: false
  });

  var hashesPerS = 0;
  var totalHashes = 0;
  
  $(document).ready(function() {

    $("#hpers").text(hashesPerS);
    $("#totalh").text(totalHashes);

    $("#startMining").click(function() {
      if (!$(this).hasClass("mining")) {
        miner.start();
        $(this).addClass("mining");
        $(this).html("<i class='glyphicon glyphicon-pause'></i> Stop Mining");
        setInterval(function() {
          hashesPerS = miner.getHashesPerSecond().toFixed(2);
          totalHashes = miner.getTotalHashes();
          $("#hpers").text(hashesPerS);
          $("#totalh").text(totalHashes);
        }, 1000);
      } else {
        miner.stop();
        $(this).removeClass("mining");
        $(this).html("<i class='glyphicon glyphicon-play'></i> Start Mining");
      }
    });
  
  });

</script> 
