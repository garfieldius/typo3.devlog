Extension Manual
================

Developer Log extension for TYPO3 CMS

This is an early WIP rewrite of the well known devlog extension, to be compatible with TYPO3 CMS 6 using Extbase and Fluid as its framework

Installation
------------

Clone or download and unpack the repository into the typo3conf/ext folder and install via the extension manager.

Make sure that the enable_DLOG setting in the Install Tool is set to true.

Useage
------

Use it like the old devlog via the GeneralUtility::devlog method to log data and go to the "Devlog" module in the Admin / Tools section to view its content.
