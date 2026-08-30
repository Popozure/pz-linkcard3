=== Pz-LinkCard3 ===
Contributors: Poporon
Tags: LinkCard, BlogCard, Internal Link, External Link, translation-ready
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 3.0.0.TEST
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.amazon.co.jp/gp/registry/wishlist/2KIBQLC1VLA9X
Text Domain: pz-linkcard3

Display links as blog cards by entering a URL in a shortcode. Say goodbye to plain text links.

== Description ==

Pz-LinkCard3 displays links as customizable blog cards.

It’s easy to use. Simply add a shortcode containing a URL, and the plugin will generate a card for that link.

You can customize the card’s design from the settings screen.

From the Cache Manager, you can edit, update, import, export, and delete cached link data.

Use rich link cards instead of plain text links.

* This plugin may send the destination URL to configured web APIs to retrieve thumbnails, site icons, and social counts. It also stores the retrieved title, excerpt, and related cached data in the database. For more details, please see the “Optional Section” below.


== Installation ==

Install from the WordPress admin screen

1. In the WordPress admin screen, go to "Plugins" -> "Add New".
2. Search for "Pz-LinkCard".
3. Confirm the plugin name and author, then click "Install Now".
4. Activate the plugin.


Install from a .zip file

1. Download the plugin from this page.
2. Save the .zip file to your computer.
3. In the WordPress admin screen, go to "Plugins" -> "Add New".
4. Click "Upload Plugin", then select the downloaded .zip file.
5. Click "Install Now", then activate the plugin.


== Frequently asked questions ==

= 内部リンクでの場合も新しいウィンドウで開きたいのですが？ =

設定画面から「外部リンク」と「内部リンク」、それぞれ「新しいタブで開く」の設定項目があります。
「モバイルのみ」と設定すると、パソコン等では別タブで開き、スマートフォンでは同一タブに開く事も出来ます。
新しいウィンドウで開くか、新しいタブで開くかは、ブラウザ側の制御となるため、ほとんどの場合新しいタブで開く事となります。


== Screenshots ==

1. Settings screen
2. Cache manager screen
3. Cache data editor
4. Example LinkCard display
5. Shortcode and URL input


== Changelog ==

= 3.0.0 =
* [Tested] WordPress 6.8.3 での動作確認を行いました。
* [Tested] WordPress 6.9 での動作確認を行いました。
* [Tested] WordPress 6.9.4 での動作確認を行いました。
* [Tested] WordPress 7.0 での動作確認を行いました。
* [Tested] WordPress 7.0.2 での動作確認を行いました。
* [Tested] WordPress 7.0.4 での動作確認を行いました。

* [Added] 一部の短縮URL（bit.ly、t.co等）に対応しました。
* [Added] リンクカード全体をリンク化している場合でも、SNSやカテゴリのリンクを個別に開けるようになりました。
* [Added] ブロックエディターに対応し、「Pz-LinkCard」ブロックをウィジェットの欄に追加しました。URLを入力中にEnterを押すとプレビュー表示されます。
* [Added] X（Twitter）のポストをリンクカード形式で埋め込み表示できるようになりました。
* [Added] 設定画面で左右のスワイプ操作でタブを切り替える機能を追加しました。
* [Added] 設定画面のタブの上でマウスホイールを回すとタブを切り替える機能を追加しました。
* [Added] 設定画面で[Ctrl]を押しながら[←][→]を押すとタブを切り替える機能を追加しました。
* [Added] 設定画面の選択肢がある項目の上で[Shift]キーを押しながらマウスホイールを回すと選択肢を変更出来る機能を追加しました。
* [Added] 設定画面右上に検索ボックスを追加しました。項目名や説明から検索し、自動でフォーカスされます。
* [Added] 設定画面のタブ上でにマウスホイールを回すことで、タブを切り替えられるようになりました。
* [Added] 設定画面の「一番上へ戻る」ボタン下に、現在開いているタブ名を表示するようになりました。
* [Added] 設定画面の「表示」タブに「投稿日＆更新日」の設定項目を追加しました。表示書式を指定できます。
* [Added] 設定画面の「配置」タブに「ブロックの間隔」を追加しました。記事情報に対するサイト情報やサムネイルとの隙間の大きさを設定出来ます。
* [Added] 設定画面の「配置」タブに「領域を囲うタグ」を追加しました。DIVの他、ARTICLE、BLOCKQUOTEなどから選択でき、モバイル用クラス名も設定可能です。
* [Added] 設定画面の「配置」タブのサイト情報に「アイコンサイズ」を追加しました。「16px」以外に「32px」「64px」が選べます。
* [Added] 設定画面の「配置」タブの「ヘッダー」の設定項目として「左の余白（外側）」「左右の余白」「上下の余白」を追加しました。
* [Added] 設定画面の「文字」タブの「カテゴリー」を追加しました。「内部リンク」の「表示する項目」で「カテゴリー」を選択したときに使用されます。
* [Added] 設定画面の「外部リンク」タブ、「内部リンク」タブの「サイト情報」に「表示する項目」を追加しました。サイト情報部分に最大5項目まで自由な順序で表示できます。
* [Added] 設定画面の「外部リンク」タブ、「内部リンク」タブの「記事内容」に「表示する項目」を追加しました。記事内容部分に最大5項目まで自由な順序で表示できます。
* [Added] 設定画面の「外部リンク」タブと「内部リンク」タブの「影」に「方向」「色」「透明度」「距離」「ぼかし」の項目を追加しました。ヘッダーの「枠線」「角丸め」「影」はカード本体に連動します。
* [Added] 設定画面の「外部リンク」タブと「内部リンク」タブの「ヘッダー」に「背景色」の項目を追加しました。
* [Added] 設定画面の「外部リンク」タブと「内部リンク」タブに「サムネイルの枠線・角丸め」を設定する項目を追加しました。
* [Added] 設定画面の「上級者向け」タブにリンク先の存在チェックの「処理する頻度」と「処理毎の件数」を追加しました。
* [Added] 設定画面の「上級者向け」タブにソーシャルカウント取得の「処理する頻度」と「処理毎の件数」を追加しました。
* [Added] 設定画面の「上級者向け」タブに「管理者バー」の項目を追加しました。管理者バーにも設定画面と管理画面へのリンクを表示します。
* [Added] 設定画面の「その他」タブの「ログファイル全削除」を追加しました。（「デバッグモード」の時のみ表示されます）
* [Added] 設定画面の「その他」タブの「画像の設定」に「解像度」を追加しました。主に外部リンクのサムネイルを保存する際の解像度を変更出来ます。
* [Added] カード管理画面の右上に「インポート」「エクスポート」ボタンをを追加しました。
* [Added] カード管理画面の検索時、数値を入力した場合、カードID及び記事IDを検索出来るようにしました。
* [Removed] 設定画面の「基本」タブから「かんたん書式設定」を削除しました。
* [Removed] 設定画面の「配置」タブから「BLOCKQUOTEで囲む」を削除しました。「全体をタグで囲む」で同じことが出来るようになりました。
* [Removed] 設定画面の「文字」タブから「桁数」を削除しました。
* [Removed] 設定画面の「同一ページ」タブを削除しました。リンク先が記事と同じ場合、通常の内部リンクの扱いになります。
* [Removed] 設定画面の「上級者向け」タブから「ファイルメニュー」を削除しました。
* [Removed] 設定画面の「サイトアイコン取得API」の「例3」を削除しました。
* [Fixed] 内部リンクでアイキャッチ画像を削除した記事の場合に空の画像を取得してしまっていたのを修正しました。
* [Fixed] ブロック エディタで投稿を保存した際に「更新に失敗しました。返答が正しいJSONレスポンスではありません」というエラーが出てしまうのを修正しました。
* [Fixed] クラシック エディタのテキストタブを有効にした際、クイックタグが表示されていなかった不具合を修正しました。
* [Fixed] 外部サイトにアクセスした際、EUC-JPだった場合の文字コード変換が上手くされなかった不具合を修正しました。
* [Fixed] カード管理画面で検索などのボタンを押すと並び順が変更されてしまう不具合を修正しました。
* [Fixed] カード管理画面でリンク切れの際にURLの先頭に「🚫」や「⚠️」が表示されない不具合を修正しました。
* [Fixed] 記事内容を取得する際のユーザーエージェントが正しくセットされていなかったのを修正しました。
* [Modified] 設定画面のタイトルを「Pz カード設定」から「Pz リンクカード設定」へ変更しました。
* [Modified] 設定画面の上部のタブが画面外にはみ出す際に折り返しだったのを折り返しせずにスクロールする方式に変更しました。
* [Modified] 設定画面の一部の項目の初期設定値（デフォルト）を変更しました。
* [Modified] 設定画面の「かんたん書式設定」を設定した際の表示を調整しました。
* [Modified] 設定画面の「基本」タブの「関連情報」の表示方法を見直し、XのDMなどで問い合わせが出来る旨を記載しました。
* [Modified] 設定画面の「表示」タブに「リンク切れの枠線」を追加しました。リンク切れの際にリンクカードの枠線がここで設定した色で表示されます。
* [Modified] 設定画面の「配置」タブを「外部リンク」タブの手前へ移動しました。
* [Modified] 設定画面の「配置」タブから「サイト名を使用」の項目を削除しました。各リンクのタブの「サイト情報」にて「サイト名」「ドメイン」が選べるようになりました。
* [Modified] 設定画面の「初期化」タブを常時表示するように変更しました。
* [Modified] 設定画面の「リンク先の検査」の「リダイレクト処理する」を「外部リンク」の「リダイレクト先からの取得」へ変更しました。
* [Modified] 設定画面で使用するカラーピッカー（カラーパレット）をオリジナルの物に変更しました。
* [Modified] 設定画面にある「付加情報」の名称を「サイト区分」に変更しました。機能に変更はありません。
* [Modified] カード管理画面のタイトルを「Pz カード管理」から「Pz リンクカード・キャッシュ管理」へ変更しました。
* [Modified] カード管理画面でドメインのプルダウンリストを変更した際、自動で抽出を実行するように変更しました。
* [Modified] 処理中の暗転と投稿編集画面の挿入ボタンについてブロック エディタのクラシック ブロックより上になるようにz-indexの数値を上げました。
* [Modified] ツールメニューにリンク切れ件数を表示させるタイミングを調整しました。
* [Modified] 内部リンクのサムネイル取得方法を変更しました。
* [Modified] 外部サイトのアクセスにcURLが使えない環境の場合はWP_REMOTE_GETを使用していましたが、cURLのみに変更しました。
* [Modified] 外部サイト・内部サイト・同ページの判定方法を修正しました。
* [Modified] URLパラメーターに使用出来るスキーム（プロトコル）をhttpとhttpsのみにしました。（以前からhttpとhttps以外は弾かれていましたが正式に仕様としました。）
* [Modified] はてなブックマーク数を取得するAPIがhttpのままだったため、httpsへ変更しました。
* [Modified] 外部リンク、内部リンクに関わらず最初にキャッシュを取得するように変更しました。
* [Modified] 管理画面で一括操作を行う際のボタンの文字を「適用」から「実行」に変更しました。
* [Modified] クリック回数をカウントする際、連続で同じリンクカードをクリックしてもカウントされなくなりました。（同じIPアドレスから1分以内に同じリンクカードをクリックしてもカウントしません。）





* [Issue] 無駄な処理の見直し。
* [Issue] スケジュール処理を見直し、記事内容の初回取り込みもバックグラウンドにする。
* [Issue] 管理者ログインしているとき、記事のリンクカード上で右クリックすると専用のメニューを表示して再取得などが出来るようにする。（課題あり）
* [Issue] パラメータの保存・呼び出し。（課題あり）
* [Issue] プラグインバージョンが代わった際、プラグインバージョンごとにパラメータを自動保存する。









= 2.5.7.2 =
* [Fixed] カード管理画面のインラインメニューが動作しない不具合を修正しました。
* [Added] カード管理画面の一覧の下側にあるページ数を復活しました。

= 2.5.7.1 =
* [Fixed] 設定画面に入った際にエラーになり画面表示されない不具合を修正しました。（Thanks jh4vaj @jh4vaj on x.com）
* [Fixed] PHP8を使用したときに文字コード判定が誤ってしまう不具合を修正しました。
* [Fixed] PHP8を使用したときにエクスポート時に警告が出てしまうのを修正しました。
* [Fixed] カード管理画面のページ数を直接入力してもページが変更出来ない不具合を修正しました。
* [Removed] カード管理画面の一覧の下側にあるページ数を削除しました。

= 2.5.7 =
* [Tested] WordPress 6.8.2 での動作確認を行いました。
* [Tested] PHP 8.1.29 での動作確認を行いました。PHPの最低要件を同バージョンとしました。
* [Tested] PHP 8.4.10 での動作確認を行いました。
* [Removed] Pocketのサービス終了に伴い、Pocketのカウント取得・表示の機能を削除しました。
* [Added] 設定画面の「リンク先の検査」タブに「クリック件数」を追加しました。クリックされた件数を取り、カード管理画面に表示します。
* [Added] カード管理画面に「クリック件数」を追加しました。
* [Added] ローカルプライベートアドレス、ループバックアドレスを指定禁止にしました。
* [Fixed] 内部リンクでカテゴリーページを指定した際の処理を見直しました。

= 2.5.6.5 =
* [Tested] WordPress 6.8 での動作確認を行いました。
* [Tested] WordPress 6.8.1 での動作確認を行いました。
* [Fixed] 設定画面からプラグインの再起動を行った際、エラーが発生するのを修正しました。
* [Fixed] 設定画面の「基本」タブの「更新履歴」で正しく改行がされていないのを修正しました。
* [Fixed] プラグインを有効化したとき、uninstallのフックを定義しようとしてエラーが表示されるのを修正しました。（Thanks ほんみや @hontonomiyazaki on x.com）

= 2.5.6.4 =
* [Fixed] クラシックエディタで「挿入ボタン」からショートコードを挿入した後、フォーカスが自動で戻るように修正しました。
* [Fixed] ブロックエディタのクラシックブロックから「挿入ボタン」を使用したときにフォーカスされるように修正しました。
* [Modified] 投稿編集画面で使用している「挿入ボタン」のjsファイルにについて、jQueryだったものをJavaScriptに変更しました。
* [Modified] 設定画面で使用しているjsファイルについて、一部の機能を分割しました。また一部、jQueryだったものをJavaScriptに変更しました。
* [Modified] 設定画面の「管理者」タブで実行出来る処理を一部変更しました。※一般に解放していません。

= 2.5.6.3 =
* [Fixed] 設定画面でタブの移動などが出来なくなる不具合を修正しました。（Thanks HidetatsuTsuji @hakitukai on x.com）
* [Fixed] 設定画面で処理中にエラーが発生したまま固まる不具合を修正しました。（Thanks ゴルフや投資の配信 @piyofumin4 on x.com）
* [Fixed] 投稿編集画面にて挿入ボタンが起因したエラーが表示されていたのを修正しました。（Thanks マーージ＠ブログ中毒 @maagemagemaaage on x.com）
* [Fixed] 投稿編集画面にて挿入ボタンが動作しない不具合を修正しました。（Thanks yukkun20 #comment-12876 on popozure.info）
* [Fixed] 管理者権限の無いログインユーザーがサイトを見た際、PHPのWarningが出てしまう不具合を修正しました。（Thanks kikorin55 @kikorin55 on wordpress.org）
* [Fixed] 軽微なバグを修正しました。
* [Modified] 設定画面の「基本」タブの「変更履歴」の表示方法を修正しました。（行の先頭のバッジに保留（PENDING）を追加しました）
* [Modified] 設定画面の「上級者向け」タブの「調査モード」をログファイルを出力する機能のみにしました。
* [Modified] 設定画面の「上級者向け」タブに「デバッグモード」を追加しました。調査モードの一部の機能（非表示項目の表示）を移しました。
* [Modified] 内部処理を一部見直しました。

= 2.5.6.2 =
* [Added] 設定画面の「上級者向け」タブに「入力禁止」を追加しました。「変更を保存」をクリックした際に誤入力を避けるため暗転して入力禁止にします。
* [Fixed] 設定画面で「変更を保存」をクリックした際、暗転するようにしましたが初期値では暗転なしにしました。
* [Fixed] 設定画面の「配置」の「幅」を空欄にしていた場合、「0px」として扱っていたのを「100%」として扱うように修正しました。（Thanks KAI #comment-12912 on popozure.info）
* [Modified] 内部処理を一部見直しました。

= 2.5.6.1 =
* [Fixed] クラシック エディターで挿入ボタンが動作しない場合があったため、スクリプトを修正しました。
* [Fixed] 「テキストリンク行を変換」を有効にした際、リンクカードが表示されずURLエラーの表示になってしまう不具合を修正しました。
* [Fixed] マルチサイトを利用している際、設定画面に「初期設定値に〇〇が定義されていません」というエラーが表示されるのを修正しました。（Thanks ふりっぷ @flip365 on x.com）

= 2.5.6 =
* [Tested] WordPress 6.7.1 での動作確認を行いました。
* [Tested] WordPress 6.7.2 での動作確認を行いました。
* [Tested] WordPress 6.7.3-alpha-59811 での動作確認を行いました。
* [Tested] PHP 7.4.33 での動作確認を行いました。PHPの最低要件を同バージョンとしました。
* [Tested] PHP 8.2.22 での動作確認を行いました。
* [Removed] 設定画面の「表示」タブから「リンク文字の下線を除去」を削除しました。「文字」タブで同様の設定が出来るようになったため。
* [Fixed] 「サイズの変更」を有効にしているときの画像の縦横比が崩れてしまっていたのを修正しました。（Thanks ささのは @sasanohasan on x.com）
* [Fixed] サムネイルに強制的に枠線が表示されてしまったのを修正しました。（Thanks さくら工作室/工作系YouTuber @skrdtrt on x.com）
* [Fixed] プラグインを有効化したときに「プラグインの有効化中にxxx文字の予期しない出力が生成されました」のエラーが表示されてしまうのを修正しました。
* [Fixed] リンク先の画像取得に失敗した際、カード管理画面でエラーが表示されてしまう不具合を修正しました。（Thanks 足じゃんけん @ASHIJANKEN on x.com）
* [Modified] 設定画面の「基本」タブの「変更履歴」を日本語のみにしました。
* [Modified] 設定画面の「基本」タブの「変更履歴」の表示方法を修正しました。（行の先頭に追加（Added）・修正（Fixed）・変更（Modified）・削除（Removed）のバッジが付きます）
* [Modified] 設定画面の「かんたん書式設定」を設定した際の表示を調整しました。
* [Modified] 設定画面の「文字」タブの構成を表形式に変更しました。
* [Modified] 設定画面の「文字」タブに「ヘッダー文字列」「カテゴリー」を追加しました。ただしカテゴリー表示は未実装のため変更出来ません。
* [Modified] 設定画面の「外部リンク」「内部リンク」「同ページへのリンク」に「ヘッダー」を追加しました。
* [Modified] 設定画面の「サイズの変更」を「表示」タブから「配置」タブへ移動しました。
* [Modified] 設定画面の「BLOCKQUOTEで囲む」を「配置」タブから「上級者向け」タブへ移動しました。
* [Modified] 設定画面の「シェア数の表示」の「タイトルの後ろ」を「タイトルの下」へ変更しました。
* [Modified] 設定画面の「表示」にある「投稿日を表示」の機能について、指定した場合、URLの代わりに表示するように修正しました。
* [Modified] スタイルシートを生成する際、軽量化したファイルも生成するように修正しました。
* [Modified] 設定画面の「上級者向け」タブに「圧縮」を追加しました。軽量化したスタイルシートを使用するようになります。
* [Modified] ショートコードのURLパラメータの指定が誤っている場合の判定を厳しくしました。エクスポート時に不正データになってしまうため。（Thanks さくら工作室/工作系YouTuber @skrdtrt on x.com）
* [Modified] 設定の初期値をいくつか変更しました。（リンクカードの影の初期値が無しから有りになったなど）
* [Modified] 設定画面で「変更を保存」を押した際、誤入力を防ぐため暗転するように修正しました。
* [Added] 設定画面の「内部リンク」タブの「記事取得方法」にカスタムフィールドを優先する設定を追加しました。（Thanks Goshi #comment-7728 on popozure.info）
* [Added] 設定画面の「内部リンク」タブに「タイトルにするカスタムフィールド」「抜粋文にするカスタムフィールド」を追加しました。（Thanks Goshi #comment-7728 on popozure.info）
* [Added] 設定画面の「表示」にサムネイルの枠線を追加しました。
* [Added] 設定画面の「エディター」タブに「抜粋文をクリア」を追加。titleパラメーターを指定したときに抜粋文をクリアします。
* [Added] 設定画面の「上級者向け」タブに「テキストの選択」を追加。カード内のテキストを選択禁止に出来ます。


== Upgrade notice ==


== Arbitrary section ==

= Display and DB cache =

This plugin creates its own database tables when it is activated. The table names use the WordPress database prefix.

When an external LinkCard is displayed for the first time, the plugin retrieves the title, excerpt, and related link information from the linked site and stores it in the database cache.

The first display may take longer because the linked site is accessed. Later displays are faster because the cached data is used.


= Create files =

Generated CSS files are stored in the plugin's custom folder under the WordPress uploads directory.


= Use Web APIs =

Social counts are retrieved by sending API requests to supported services.

* Facebook ... https://graph.facebook.com?fields=og_object{engagement}&id=[URL]

* Hatena ... https://api.b.st-hatena.com/entry.count?url=[URL]

Site icons can be retrieved by using the configured site icon API. The default setting uses Google's favicon service, and the API URL can be changed in the settings.

Thumbnails can be retrieved by using the configured thumbnail API. The default setting uses the WordPress.com mShots service, and the API URL can be changed in the settings.


= 表示とキャッシュ =

このプラグインは、有効化したときにDBテーブルを一つ作成します。（プレフィックス＋「pz_linkcard」）

外部リンクを設定した場合、記事のページを開いて「初めて表示された」ときに、リンク先のサイトからタイトル・抜粋文を取得してDBへキャッシュします。

外部リンクを設定した場合、カードの枚数分だけ外部サイトへのアクセスが発生するため多量のリンクを作成すると、記事をプレビューした時等、最初の表示に時間がかかります。

次回の表示はDBキャッシュから行うので高速に表示を行います。

（内部でのDBアクセスが発生しますが、通常は軽微なものです。カード1枚表示のたびに、取得のために1クエリ発行します。更新が発生した場合には挿入・更新のためのクエリが1回発生します。）


= ソーシャルカウントの取得 =

ソーシャルカウントについては、「facebook（フェイスブック）のシェア数」「はてなブックマークのブックマーク数」の2種類に対応しています。

それぞれWebAPIを使用して値を取得します。

バックグラウンドで取得するため、ページの表示速度には影響がありません。

取得した値はタイトルや抜粋文と同様、DBへキャッシュを行うため、直近の表示にはWebAPIアクセスが発生しません。

ソーシャルカウントの再取得は、最後の取得から4時間～36時間程度のランダムな時間で行います。

また、各WebAPIについては、仕様変更やサービス終了に伴い、正常に取得できなくなる場合があります。


= 画像取得WebAPIの利用 =

設定画面からサムネイル取得WebAPIが指定出来ます。

「WebAPIを利用する」にする事でページのスクリーンショット画像を取得します。

参考．画像取得WebAPIの設定について https://popozure.info/20151004/9317


設定画面からサイトアイコン取得WebAPIが指定出来ます。

サイトアイコンの場所はサイトによってバリエーションが多いため、WebAPIを使用する前提となります。

正式に公開されているWebAPIでは無いため、仕様変更やサービス終了に伴い、正常に取得できなくなく場合があります。


= その他 =

Pz-HatenaBlogCard からの設定引き継ぎ機能はありません。この機会に触った事のなかった設定項目にも触れていただければ幸いです。

ショートコードを変える事で、Pz-HatenaBlogCard と併用利用する事ができますが、通常はリソース消費が増えるだけなので、推奨はしません。


ショートコード内にURLを記述した場合、WordPressピンバックは飛びません。


設定項目については、WordPress標準の options に設定内容を保存します。キーは「Pz-LinkCard_options」の1レコードです。


なお、アンインストールを行う際には、キャッシュを保管するDBテーブルと、options内の設定ファイルは削除されます。

アンインストール時の削除に関してはプラグインディレクトリ内の uninstall.php で行っています。
