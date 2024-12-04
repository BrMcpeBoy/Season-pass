<?php

namespace TungstenVn\SeasonPass\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use TungstenVn\SeasonPass\SeasonPass;
use TungstenVn\SeasonPass\subCommands\addItem;
use TungstenVn\SeasonPass\menuHandle\menuHandle;
use TungstenVn\SeasonPass\subCommands\removeItem;
use TungstenVn\SeasonPass\subCommands\setItemInfo;
use jojoe77777\FormAPI\SimpleForm;

class commands extends Command implements PluginOwned, Listener {

    /*  Main Class (SeasonPass) */
    public $ssp;

    public function __construct(SeasonPass $ssp){
        parent::__construct("seasonpass", "Season Pass", ("/seasonpass help"), ["ssp"]);
        $this->setPermission("seasonpass.command");
        $this->ssp = $ssp;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args){
        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $a = new menuHandle($this, $sender);
                $this->ssp->getServer()->getPluginManager()->registerEvents($a, $this->ssp);
                return;
            }
            switch ($args[0]) {
                case 'a':
                case 'additem':
                    new addItem($this, $sender, $args);
                    break;
                case 'sl':
                case 'setlore':
                    new setItemInfo(1, $sender, $args);
                    break;
                case 'sn':
                case 'setname':
                    new setItemInfo(0, $sender, $args);
                    break;
                case 'r':
                case 'removeitem':
                    new removeItem($this, $sender, $args);
                    break;
                default:
                    $this->helpForm($sender,"");
                    break;
            }
        } else {
            $sender->sendMessage("Chạy lệnh trong game.");
        }
    }
    public function helpForm(Player $player,string $txt){
        $form = new SimpleForm(function(Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    $player->sendMessage("§a§l【Ｓeason Ｐass】 §r§eChúc bạn một ngày tốt lành :3");
                    break;
                case 1:
                    $this->helpForm($player,"\n§aBạn có thể đọc lại để hiểu hơn :3, vì mình chả biết chỉ ntn luôn ấy\n");
                    break;
                default:
                    break;
            }
        });
        $form->setTitle("§lＴHẺ ＭÙA - HƯỚNG DẪN");
        $form->setContent($txt."\n§eTại menu này, bạn có thể hiểu thêm về thẻ mùa - hoặc còn gọi là thẻ mùa giải theo tháng\n\n§e-§3 Về thẻ mùa của các game:\n§7Bạn đã từng biết về những game theo phong cách moba, sinh tồn bắn súng, v.v. Và những game đó luôn luôn có một tính năng được gọi là §ethẻ mùa §7;-;. §eThẻ mùa§7 được dùng như dạng nhiệm vụ, dùng cho mem §ecày cuốc lấy điểm rồi nhận vật phẩm§7....\n\n§e-§3 Có mấy loại thẻ mùa :v?\n§7Theo mình biết, hiện có 2 loại thẻ mùa trong server này. Đó là thẻ §fthông thường§7 và §ethẻ huyền thoại§7, thẻ §fthông thường §7dành cho những người cày miễn phí, nhưng vật phẩm vẫn sẽ xứng theo công sức bạn cày, còn thẻ §ehuyền thoại §7được ví như là thẻ nâng cấp so với thẻ §fthông thường§7, có thể mua thẻ này bằng §6POINTS §7và có thể nhận song song thẻ thường và thẻ huyền thoại, và vật phẩm ở thẻ huyền thoại sẽ tăng thêm và ngon hơn :v nhưng vẫn cân bằng đóa không có lạm phát đâu, và vật phẩm ở thẻ §ehuyền thoại§7 có giá trị hơn số point bạn bỏ ra mua ấy :3\n\n§e-§3 Làm sao để tăng cấp thẻ mùa:\n§7Bạn chỉ cần mine :v, không sai. Bạn chỉ cần mine thôi và cấp bạn có thể tăng tùy theo block bạn đã mineee\n\n"
        );
        $form->addButton("§lĐÃ HIỂU [:3]", 0, "textures/items/light_block_7");
        $form->addButton("§lCHƯA HIỂU [:v?]", 0, "textures/items/light_block_0");
        $player->sendForm($form);
        return $form;
    }
    public function getOwningPlugin() : SeasonPass {
        return $this->ssp;
    }
}
