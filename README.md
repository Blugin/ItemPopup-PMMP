[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

[![icon/192x192](meta/icon/192x192.png?raw=true)]()

[![License](https://img.shields.io/github/license/PMMPPlugin/ItemPopup.svg?label=License)](LICENSE)
[![Release](https://img.shields.io/github/release/PMMPPlugin/ItemPopup.svg?label=Release)](https://github.com/PMMPPlugin/ItemPopup/releases/latest)
[![Download](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/total.svg?label=Download)](https://github.com/PMMPPlugin/ItemPopup/releases/latest)


A plugin show custom item popup for PocketMine-MP

## Command
Main command : `/itempopup <set | remove | list | lang | reload | save>`

| subcommand | arguments                             | description            |
| ---------- | ------------------------------------- | ---------------------- |
| Set        | \<item id\> \<item damage\> \<popup\> | Set item's popup       |
| Remove     | \<item id\> \<item damage\>           | Remove item's popup    |
| List       | \[page\]                              | Show item popup list   |
| Lang       | \<language prefix\>                   | Load default lang file |
| Reload     |                                       | Reload all data        |
| Save       |                                       | Save all data          |




## Permission
| permission           | default | description       |
| -------------------- | ------- | ----------------- |
| itempopup.cmd        | OP      | main command      |
|                      |         |                   |
| itempopup.cmd.set    | OP      | set  subcommand   |
| itempopup.cmd.remove | OP      | remove subcommand |
| itempopup.cmd.list   | OP      | list subcommand   |
| itempopup.cmd.lang   | OP      | lang subcommand   |
| itempopup.cmd.reload | OP      | reload subcommand |
| itempopup.cmd.save   | OP      | save subcommand   |




## ChangeLog
### v1.0.0 [![Source](https://img.shields.io/badge/source-v1.0.0-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.0.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.0.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.0.0)
- First release
  
  
---
### v1.0.1 [![Source](https://img.shields.io/badge/source-v1.0.1-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.0.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.0.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.0.1)
- \[Added\] Support korean
- \[Changed\] Some code
  
  
---
### v1.0.2 [![Source](https://img.shields.io/badge/source-v1.0.2-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.0.2) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.0.2/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.0.2)
- \[Fixed\] Some error
  
  
---
### v1.1.0 [![Source](https://img.shields.io/badge/source-v1.1.0-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.1.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.1.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.1.0)
- \[Removed\] Return type hint ans SQLITE
- \[Fixed\] Some error
  
  
---
### v1.1.1 [![Source](https://img.shields.io/badge/source-v1.1.1-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.1.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.1.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.1.1)
- \[Fixed\] Not save when plugin disable
  
  
---
### v1.2.0 [![Source](https://img.shields.io/badge/source-v1.2.0-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.2.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.2.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.2.0)
- \[Fixed\] main command config not work
- \[Changed\] translation method
- \[Changed\] command structure
  
  
---
### v1.2.1 [![Source](https://img.shields.io/badge/source-v1.2.1-blue.png?label=source)](https://github.com/PMMPPlugin/ItemPopup/tree/v1.2.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/ItemPopup/v1.2.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/ItemPopup/releases/v1.2.1)
- \[Changed\] Add return type hint
- \[Fixed\] Violation of PSR-0
- \[Changed\] Rename main class to ItemPopup
- \[Added\] Add PluginCommand getter and setter
- \[Added\] Add getters and setters to SubCommand
- \[Fixed\] Add api 3.0.0-ALPHA11
- \[Added\] Add website and description
- \[Changed\] Show only subcommands that sender have permission to use
