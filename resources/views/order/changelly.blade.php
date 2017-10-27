<link rel="stylesheet" href="https://changelly.com/widget.css"/>
        <a id="changellyButton" href="https://changelly.com/widget/v1?auth=email&from=BTC&to=ETH&merchant_id=3f59685c3aad&address=&amount=1&ref_id=3f59685c3aad&color=20232b" target="_blank">
          <img src="https://changelly.com/pay_button_exchange.png" />
        </a>
        <div id="changellyModal">
          <div class="changellyModal-content">
            <span class="changellyModal-close">x</span>
            <iframe
              src="https://changelly.com/widget/v1?auth=email&from=BTC&to=ETH&merchant_id=3f59685c3aad&address=&amount=1&ref_id=3f59685c3aad&color=20232b"
              width="600"
              height="500"
              class="changelly"
              scrolling="no"
              style="overflow-y: hidden; border: none"
            >
              Can't load widget
            </iframe>
          </div>
          <script type="text/javascript">
            var changellyModal = document.getElementById('changellyModal');
            var changellyButton = document.getElementById('changellyButton');
            var changellyCloseButton = document.getElementsByClassName('changellyModal-close')[0];
            changellyCloseButton.onclick = function() {
                changellyModal.style.display = 'none';
            };

            changellyButton.onclick = function widgetClick(e) {
              e.preventDefault();
              changellyModal.style.display = 'block';
            };
          </script>
        </div>
