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