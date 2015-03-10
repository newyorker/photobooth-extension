Photo Booth Extension
==========

A browser extension for [Photo Booth](http://www.newyorker.com/culture/photo-booth/) that takes over any new tab you open, fills it with a collection of rich photography from the Photo Booth pages.

Installing
-----

Install direct from the [Chrome Web Store](https://chrome.google.com/webstore/detail/photo-booth-the-new-yorke/ecnlpbkkcihngfehdimchlekdclbofjb)

To access development releases, simply download or clone this code and load as an unpacked extension.

Source Code: [https://github.com/newyorker/photobooth-extension](https://github.com/newyorker/photobooth-extension)

Unpacked Extension
-----

- If you downloaded the code, unzip the file.
- Open (chrome://extensions/) or select the menu `Window > Extensions`.
- Enable the Developer Mode at top-right.
- Click `Load unpacked extension...` and select the folder.
- Open a New Tab

Notes
-----

The current extension pulls a feed from Dropbox that is updated every X number of minutes. This will eventually change to a newyorker.com URL once we complete the switcher-over from http to https. Until then this setup works very well.

A copy of the feed exists in the 'service' directory.

Issues
-----

There are no known issues at this time.

Contributing
-----

Fork at it.

License
-----

MIT