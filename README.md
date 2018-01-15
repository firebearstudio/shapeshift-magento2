# ShapeShift for Magento2 - Accept BitCoin, Ethereum and other cryptocurrencies without transations fee and registration

<img src="https://firebearstudio.com/blog/wp-content/uploads/2017/10/Firebear-ShapeShift.png" width="200" />

- Accept all possible altcoins on Magento 2 websites;
- Support for all major cryptocurrencies;
- No login or registration;
- No transaction fee;
- Deposit minimum/maximum;
- No need to use multiple wallets, code branches or databases;
- Magento 2 payment method developed with the best practices in mind; 
- Easy integration 
- 100% Free & Open Source - available on GitHub

<a href="https://www.youtube.com/watch?v=dMk2T-06kxM" target="_blank">Video overview</a>

Extension compatible with all recent versions of Magento 2.0.x , 2.1.x, 2.2.x Open Source (Community), Commerce (Enterprise) and Cloud Edition!

<b>At this moment to accept BitCoin (BTC) with this extension you need to enter wallet of different cryptocurrency to receive (we advise Ethereum) - amounts of placed orders will be converted automatically without additional fees (only miner fee) by ShapeShift. Currently, BitCoins fee are very high and also network is slow, so consider Ethereum and other altcoins!</b>

Alternative payment gateway where you can directly accept BitCoin and altcoins with registration, friendly interface, and withdrawal to fiat - <a href="https://firebearstudio.com/coinpayments-for-magento-2-bitcoin-ethereum-cryptocurrency.html">CoinPayments for Magento 2</a>

Stay up to date about the crypto by follow top communities on Reddit  - <a href="https://www.reddit.com/r/cryprocurrency" target="_blank">Cryptocurrency</a> | <a href="https://www.reddit.com/r/ethereum" target="_blank">Ethereum</a> | <a href="https://www.reddit.com/r/bitcoin" target="_blank">BitCoin</a>

Accept cryptocurrency payments on Magento 2 by ShapeShift exchange API. <a href="https://firebearstudio.com/blog/firebear-shapeshift-magento-2-extension.html">Read more on our blog</a> 

Meet the advanced Bitcoin payment option for your ecommerce website - ShapeShift Magento 2 extension. It is a cryptocurrency converter that supports Bitcoin, Ethereum, and tons of other altcoins. Learn what is BitCoin and create BitCoin wallet - https://www.bitcoin.com/ 

<a href="https://shapeshift.io" target="_blank">ShapeShift</a> collects neither personal data nor customer funds: the exchange takes place beyond company accounts. Note that most digital currency trading companies collect both information and funds, so ShapeShift introduces a great competitive advantage over them making transactions much more secure.

It doesn’t require name, email, or location to send funds. A specific address to which you should send funds. The need to create an account to run a transaction is completely eliminated.
A “No Fiat” policy is another feature of the platform and module. But you can run fiat withdrawal via other services that support fiat, for instance, Coinbase. Thus, the usage of banks or political currencies is eliminated within the platform.

With ShapeShift, what you see is what you get. The exchange rate shown is exactly what you'll receive, minus only the "miner fee." So you don't pay any transaction fee which is typical for traditional payment gateways! Learn more - https://info.shapeshift.io/about 

Supported coins : 1ST,ANT,BAT,BCH,BTC,BCY,BLK,BNT,BTS,CLAM,CVC,DASH,DCR,DGB,DGD,DOGE,EDG,EMC,EOS,ETH,ETC,FCT,FUN,GAME,GNO,GNT,GUP,ICN,KMD,LBC,LSK,LTC,MAID,MLN,MSCN,MONA,MTL,NMC,NMR,NVC,PAY,USNBT,NXT,OMG,POT,PPC,QTUM,RDD,REP,RLC,SC,SJCX,SNGLS,SNT,START,STEEM,SWT,TKN,USDT,VRC,VTC,VOX,TRST,WAVES,WINGS,XCP,XMR,XRP,ZEC,ZRX
Learn more about all coins & see current exchange rate on <a href="https://coinmarketcap.com/" target="_blank">CoinMarketCap</a>

<b>ShapeShift for Magento 2 Installation</b>

Run:
```
composer require firebear/shapeshift
```
``` 
php -f bin/magento setup:upgrade
```
```
php -f bin/magento setup:static-content:deploy
```
```
php -f bin/magento cache:clean
```
<b>Checkout integration</b>

To improve the default shopping experience of Magento 2, the Firebear ShapeShift extension adds a new payment method to the checkout page.  Customers can select it after completing the first checkout step. It is necessary to specify a cryptocurrency to place the order as well as a return address to enable further refund. 

<img src="https://firebearstudio.com/blog/wp-content/uploads/2017/10/Magento-2-ShapeShift-Checkout.gif" alt="Magento 2 BitCoin & Ethereum checkout" title="Accept BitCoin on Magento 2">

To continue the checkout procedure, a customer should hit the ‘Place Order’ button that redirects a buyer to a new screen. On this screen, a deposit address and a required amount of altcoins are displayed. This information is necessary to complete the order.

<img src="https://firebearstudio.com/blog/wp-content/uploads/2017/10/Magento-2-ShapeShift-Integration.jpg" alt="place order with BitCoin Magento 2" title="BitCoin integration Magento 2" />

As for additional order details and tracking, they are provided via email. 

<b>Backend configuration</b>

Now, let’s tell a few words about the backend configuration of the Firebear Magento 2 ShapeShift extension. Everything is even easier here. From the perspective of a store administrator, the configuration of the ShapeShift Magento 2 extension doesn’t takes too much time and effort. All the necessary settings are available under Stores -> Settings -> Configuration -> Sales -> Payment Methods -> Other Payment Methods. There is a new payment method called ‘Shape Shift Payment’. The appropriate tab allows you enabling the extension and selecting the desired cryptocurrency you want to get after the payment is processed. The ShapeShift Magento 2 module supports all existing altcoins. Of course, you can select Bitcoin or Ethereum as a basis of all operations, but the plugin offers much wider opportunities. Next, specify a wallet deposit address. Remember that it must be related to the specified coin type.

<img src="https://firebearstudio.com/blog/wp-content/uploads/2017/10/Magento-2-ShapeShift-Backend.gif" alt="Magento 2 BitCoin extgension configuration" title="Magento 2 admn bitcoin" />

Next, it is necessary to specify a paid order status, turn the debugging on or off, and select a payment action. 

<img src="https://firebearstudio.com/blog/wp-content/uploads/2017/10/Magento-2-ShapeShift-Order-Status.jpg" alt="BitCoin order status Magento 2" title="Ethereum integration Magento 2" />

Also specify countries to allows the new payment method. Note that you can enable the ShapeShift payment method for all countries or select ones that suits your ecommerce requirements. Set the priority of the new payment method. That’s the end of the configuration.

<img src="https://firebearstudio.com/blog/wp-content/uploads/2017/10/Magento-2-ShapeShift-Applicable-Countries.gif" alt="Magento 2 bitcoin payment settings" />

Let’s compare the ShapeShift Magento 2 extension with the <a href="https://firebearstudio.com/coinpayments-for-magento-2-bitcoin-ethereum-cryptocurrency.html">FireBear CoinPayments for Magento 2 extension</a>. Both extensions allows you to accept cryptocurrencies on the basis of a Magento 2 website in a very user-friendly manner, but both have some unique features. While CoinPayments offers a user-friendly web interface, it is necessary to complete the additional checkout steps outside of the Magento 2 website within a customer-friendly interface, so your clients can easily complete the purchase. You get a wallet for cryptocurrencies and can withdraw them as a fiat currency (the commission is 0.5%) right to a bank account. The registration on the platform is required. 
In its turn ShapeShift doesn't require any registrations, but provides neither wallets (so a third-party wallet is necessary), nor the ability to withdraw fiat money. You can accept altcoins right after the module is installed and there is no limitation in terms of supported altcoins. Any coins used by your customers to complete the purchase will be converted into the specified cryptocurrency. For further information, check the <a href="https://firebearstudio.com/blog/firebear-shapeshift-magento-2-extension-manual.html">extension manual</a> or <a href="https://firebearstudio.com/contacts">contact us</a> 



