<?php
// This is file installs the map data for the Perikles variant
defined('IN_CODE') or die('This script can not be run by itself.');
require_once("variants/install.php");

InstallTerritory::$Territories=array();
$countries=$this->countries;
$territoryRawData=array(
	array('Abydus', 'Coast', 'No', 0, 1286, 818, 643, 409),
	array('Achaia', 'Coast', 'Yes', 0, 594, 366, 297, 180),
	array('Anticyra', 'Land', 'No', 0, 664, 48, 332, 24),
	array('Arcadia', 'Land', 'Yes', 0, 614, 618, 307, 309),
	array('Arcania', 'Coast', 'Yes', 0, 308, 257, 118, 74),
	array('Argos', 'Coast', 'No', 2, 810, 664, 405, 332),
	array('Argulicus Sinus', 'Sea', 'No', 0, 1084, 776, 542, 388),
	array('Athenae', 'Coast', 'Yes', 3, 1274, 534, 637, 267),
	array('Byzantinum', 'Coast', 'Yes', 0, 1296, 736, 648, 368),
	array('Callium', 'Land', 'Yes', 1, 634, 216, 317, 108),
	array('Calydon', 'Coast', 'Yes', 1, 466, 310, 233, 155),
	array('Caria', 'Coast', 'Yes', 3, 1300, 1082, 649, 536),
	array('Corinth', 'Coast', 'Yes', 0, 910, 514, 455, 257),
	array('Corsea', 'Coast', 'Yes', 0, 922, 156, 466, 78),
	array('Cumae', 'Coast', 'Yes', 0, 70, 858, 35, 429),
	array('Cyn.', 'Coast', 'Yes', 0, 832, 714, 418, 355),
	array('Cypurissius Sinus', 'Sea', 'No', 0, 364, 872, 182, 436),
	array('Dafni', 'Land', 'Yes', 5, 484, 514, 242, 257),
	array('Delion', 'Coast', 'Yes', 4, 1118, 308, 559, 154),
	array('Doloria', 'Land', 'No', 0, 550, 32, 275, 16),
	array('Doris', 'Land', 'No', 0, 712, 142, 356, 71),
	array('Elea', 'Coast', 'No', 0, 114, 938, 57, 469),
	array('Eleusis', 'Coast', 'No', 0, 1096, 400, 548, 200),
	array('Elis', 'Coast', 'Yes', 5, 364, 470, 182, 235),
	array('Epictetus', 'Land', 'No', 1, 530, 126, 265, 63),
	array('Epidaurus', 'Coast', 'Yes', 2, 946, 602, 473, 298),
	array('Epidaurus (North Coast)', 'Coast', 'No', 2, 1006, 592, 503, 296),
	array('Epidaurus (South Coast)', 'Coast', 'No', 2, 904, 668, 452, 334),
	array('Eubiea Occidental', 'Coast', 'Yes', 0, 1092, 118, 546, 59),
	array('Eubiea Oriental', 'Coast', 'No', 0, 1336, 302, 668, 151),
	array('Euboeius Sinus', 'Sea', 'No', 0, 1094, 206, 547, 103),
	array('Halieis', 'Coast', 'No', 2, 1074, 674, 537, 337),
	array('Helicon', 'Coast', 'Yes', 4, 924, 346, 462, 173),
	array('Helos', 'Coast', 'No', 6, 846, 946, 423, 473),
	array('Ilia', 'Coast', 'No', 5, 380, 560, 190, 280),
	array('Ionia', 'Coast', 'Yes', 3, 1248, 958, 624, 479),
	array('Ira', 'Coast', 'Yes', 7, 500, 762, 250, 381),
	array('Iria', 'Coast', 'Yes', 2, 1028, 666, 508, 331),
	array('Iria (North Coast)', 'Coast', 'No', 2, 1048, 648, 524, 324),
	array('Iria (South Coast)', 'Coast', 'No', 2, 976, 696, 488, 348),
	array('Italus Sinus', 'Sea', 'No', 0, 200, 1096, 100, 548),
	array('Ithaca Sinus', 'Sea', 'No', 0, 202, 414, 101, 207),
	array('Kaphyae', 'Land', 'No', 0, 670, 452, 335, 226),
	array('Khora', 'Coast', 'No', 7, 482, 872, 241, 436),
	array('Koidaunas', 'Coast', 'Yes', 6, 948, 936, 474, 468),
	array('Laconia', 'Coast', 'No', 6, 722, 944, 361, 472),
	array('Laconicus Sinus', 'Sea', 'No', 0, 850, 1054, 425, 527),
	array('Lesbos', 'Coast', 'No', 0, 1252, 884, 626, 442),
	array('Locris', 'Coast', 'Yes', 0, 672, 300, 336, 150),
	array('Lycaion', 'Land', 'No', 0, 560, 666, 280, 333),
	array('Maniana', 'Land', 'Yes', 0, 676, 610, 338, 305),
	array('Marathon', 'Coast', 'No', 0, 1260, 386, 630, 193),
	array('Mare Adriaticum', 'Sea', 'No', 0, 268, 900, 131, 504),
	array('Mare Aega', 'Sea', 'No', 0, 1438, 354, 712, 173),
	array('Mare Mediterranea', 'Sea', 'No', 0, 1388, 546, 694, 273),
	array('Marmora Sinus', 'Sea', 'No', 0, 1220, 828, 610, 414),
	array('Megalopolis', 'Land', 'Yes', 0, 648, 784, 324, 392),
	array('Megara', 'Coast', 'Yes', 0, 1050, 428, 525, 214),
	array('Megara (East Coast)', 'Coast', 'No', 0, 1094, 452, 547, 226),
	array('Megara (West Coast)', 'Coast', 'No', 0, 1012, 410, 506, 205),
	array('Messena', 'Coast', 'Yes', 7, 570, 886, 285, 443),
	array('Messeniacus Sinus', 'Sea', 'No', 0, 630, 1056, 315, 528),
	array('Mycenae', 'Land', 'Yes', 2, 818, 538, 409, 269),
	array('Nicopolicus Sinus', 'Sea', 'No', 0, 198, 224, 99, 112),
	array('Opus', 'Land', 'Yes', 4, 968, 264, 484, 132),
	array('Orchenenus', 'Land', 'No', 0, 746, 558, 373, 279),
	array('Patcae', 'Coast', 'No', 0, 520, 398, 260, 199),
	array('Pellea', 'Coast', 'Yes', 0, 664, 408, 332, 204),
	array('Phocis', 'Coast', 'Yes', 0, 816, 276, 408, 138),
	array('Phthiotis', 'Coast', 'No', 0, 854, 54, 427, 27),
	array('Pisatis', 'Coast', 'Yes', 5, 406, 600, 203, 300),
	array('Prastos', 'Coast', 'Yes', 6, 900, 854, 450, 427),
	array('Prian Rhium', 'Sea', 'No', 0, 320, 426, 160, 213),
	array('Proschium', 'Coast', 'No', 1, 382, 292, 191, 146),
	array('Protilae', 'Land', 'No', 0, 554, 548, 277, 274),
	array('Pylos', 'Coast', 'Yes', 7, 550, 978, 275, 489),
	array('Rhegium', 'Coast', 'Yes', 0, 150, 976, 75, 488),
	array('Rhodes', 'Coast', 'No', 3, 1342, 1140, 671, 570),
	array('Rhodius Sinus', 'Sea', 'No', 0, 1198, 1036, 599, 518),
	array('Saronieus Sinus', 'Sea', 'No', 0, 1172, 612, 586, 306),
	array('Sicily', 'Coast', 'Yes', 0, 46, 1082, 23, 537),
	array('Sinus Corinthiucus', 'Sea', 'No', 0, 782, 374, 391, 187),
	array('Sparta', 'Land', 'Yes', 6, 802, 860, 401, 430),
	array('Tarentum', 'Coast', 'No', 0, 182, 898, 91, 449),
	array('Tegea', 'Land', 'Yes', 0, 740, 682, 370, 341),
	array('Thebae', 'Coast', 'No', 4, 1034, 366, 517, 183),
	array('Thermas Sinus', 'Sea', 'No', 0, 1222, 114, 619, 66),
	array('Thermium', 'Land', 'Yes', 1, 414, 132, 207, 66),
	array('Thuria', 'Coast', 'No', 7, 646, 874, 323, 437),
	array('Triphylia', 'Coast', 'No', 5, 494, 694, 247, 347),
	array('Zazynthus Sinus', 'Sea', 'No', 0, 266, 662, 133, 331)
);

foreach($territoryRawData as $territoryRawRow)
{
	list($name, $type, $supply, $countryID, $x, $y, $sx, $sy)=$territoryRawRow;
	new InstallTerritory($name, $type, $supply, $countryID, $x, $y, $sx, $sy);
}
unset($territoryRawData);

$bordersRawData=array(
	array('Abydus','Byzantinum','Yes','Yes'),
	array('Abydus','Lesbos','Yes','Yes'),
	array('Abydus','Marmora Sinus','Yes','No'),
	array('Achaia','Elis','No','Yes'),
	array('Achaia','Kaphyae','No','Yes'),
	array('Achaia','Patcae','Yes','Yes'),
	array('Achaia','Pellea','Yes','Yes'),
	array('Achaia','Prian Rhium','Yes','No'),
	array('Achaia','Protilae','No','Yes'),
	array('Achaia','Sinus Corinthiucus','Yes','No'),
	array('Anticyra','Callium','No','Yes'),
	array('Anticyra','Corsea','No','Yes'),
	array('Anticyra','Doloria','No','Yes'),
	array('Anticyra','Doris','No','Yes'),
	array('Anticyra','Epictetus','No','Yes'),
	array('Anticyra','Phthiotis','No','Yes'),
	array('Arcadia','Lycaion','No','Yes'),
	array('Arcadia','Maniana','No','Yes'),
	array('Arcadia','Megalopolis','No','Yes'),
	array('Arcadia','Protilae','No','Yes'),
	array('Arcania','Doloria','No','Yes'),
	array('Arcania','Nicopolicus Sinus','Yes','No'),
	array('Arcania','Prian Rhium','Yes','No'),
	array('Arcania','Proschium','Yes','Yes'),
	array('Arcania','Thermium','No','Yes'),
	array('Argos','Argulicus Sinus','Yes','No'),
	array('Argos','Cyn.','Yes','Yes'),
	array('Argos','Epidaurus','No','Yes'),
	array('Argos','Epidaurus (South Coast)','Yes','No'),
	array('Argos','Mycenae','No','Yes'),
	array('Argos','Orchenenus','No','Yes'),
	array('Argos','Tegea','No','Yes'),
	array('Argulicus Sinus','Cyn.','Yes','No'),
	array('Argulicus Sinus','Epidaurus (South Coast)','Yes','No'),
	array('Argulicus Sinus','Halieis','Yes','No'),
	array('Argulicus Sinus','Iria (South Coast)','Yes','No'),
	array('Argulicus Sinus','Laconicus Sinus','Yes','No'),
	array('Argulicus Sinus','Mare Mediterranea','Yes','No'),
	array('Argulicus Sinus','Prastos','Yes','No'),
	array('Argulicus Sinus','Rhodius Sinus','Yes','No'),
	array('Argulicus Sinus','Saronieus Sinus','Yes','No'),
	array('Athenae','Eleusis','Yes','Yes'),
	array('Athenae','Marathon','Yes','Yes'),
	array('Athenae','Mare Mediterranea','Yes','No'),
	array('Athenae','Saronieus Sinus','Yes','No'),
	array('Byzantinum','Marmora Sinus','Yes','No'),
	array('Callium','Calydon','No','Yes'),
	array('Callium','Doris','No','Yes'),
	array('Callium','Epictetus','No','Yes'),
	array('Callium','Locris','No','Yes'),
	array('Callium','Thermium','No','Yes'),
	array('Calydon','Locris','Yes','Yes'),
	array('Calydon','Prian Rhium','Yes','No'),
	array('Calydon','Proschium','Yes','Yes'),
	array('Calydon','Thermium','No','Yes'),
	array('Caria','Ionia','Yes','Yes'),
	array('Caria','Rhodes','Yes','Yes'),
	array('Caria','Rhodius Sinus','Yes','No'),
	array('Corinth','Epidaurus','No','Yes'),
	array('Corinth','Epidaurus (North Coast)','Yes','No'),
	array('Corinth','Megara','No','Yes'),
	array('Corinth','Megara (East Coast)','Yes','No'),
	array('Corinth','Megara (West Coast)','Yes','No'),
	array('Corinth','Mycenae','No','Yes'),
	array('Corinth','Orchenenus','No','Yes'),
	array('Corinth','Pellea','Yes','Yes'),
	array('Corinth','Saronieus Sinus','Yes','No'),
	array('Corinth','Sinus Corinthiucus','Yes','No'),
	array('Corsea','Delion','Yes','Yes'),
	array('Corsea','Doris','No','Yes'),
	array('Corsea','Euboeius Sinus','Yes','No'),
	array('Corsea','Opus','No','Yes'),
	array('Corsea','Phocis','No','Yes'),
	array('Corsea','Phthiotis','Yes','Yes'),
	array('Cumae','Elea','Yes','Yes'),
	array('Cumae','Italus Sinus','Yes','No'),
	array('Cyn.','Prastos','Yes','Yes'),
	array('Cyn.','Sparta','No','Yes'),
	array('Cyn.','Tegea','No','Yes'),
	array('Cypurissius Sinus','Ira','Yes','No'),
	array('Cypurissius Sinus','Italus Sinus','Yes','No'),
	array('Cypurissius Sinus','Khora','Yes','No'),
	array('Cypurissius Sinus','Messeniacus Sinus','Yes','No'),
	array('Cypurissius Sinus','Pisatis','Yes','No'),
	array('Cypurissius Sinus','Pylos','Yes','No'),
	array('Cypurissius Sinus','Triphylia','Yes','No'),
	array('Cypurissius Sinus','Zazynthus Sinus','Yes','No'),
	array('Dafni','Elis','No','Yes'),
	array('Dafni','Ilia','No','Yes'),
	array('Dafni','Pisatis','No','Yes'),
	array('Dafni','Protilae','No','Yes'),
	array('Delion','Eleusis','No','Yes'),
	array('Delion','Euboeius Sinus','Yes','No'),
	array('Delion','Marathon','Yes','Yes'),
	array('Delion','Opus','No','Yes'),
	array('Delion','Thebae','No','Yes'),
	array('Doloria','Epictetus','No','Yes'),
	array('Doloria','Phthiotis','No','Yes'),
	array('Doloria','Thermium','No','Yes'),
	array('Doris','Locris','No','Yes'),
	array('Doris','Phocis','No','Yes'),
	array('Elea','Italus Sinus','Yes','No'),
	array('Elea','Rhegium','Yes','Yes'),
	array('Elea','Tarentum','No','Yes'),
	array('Eleusis','Marathon','No','Yes'),
	array('Eleusis','Megara','No','Yes'),
	array('Eleusis','Megara (East Coast)','Yes','No'),
	array('Eleusis','Saronieus Sinus','Yes','No'),
	array('Eleusis','Thebae','No','Yes'),
	array('Elis','Ilia','Yes','Yes'),
	array('Elis','Patcae','Yes','Yes'),
	array('Elis','Prian Rhium','Yes','No'),
	array('Elis','Protilae','No','Yes'),
	array('Elis','Zazynthus Sinus','Yes','No'),
	array('Epictetus','Thermium','No','Yes'),
	array('Epidaurus','Iria','No','Yes'),
	array('Epidaurus','Mycenae','No','Yes'),
	array('Epidaurus (North Coast)','Iria (North Coast)','Yes','No'),
	array('Epidaurus (North Coast)','Saronieus Sinus','Yes','No'),
	array('Epidaurus (South Coast)','Iria (South Coast)','Yes','No'),
	array('Eubiea Occidental','Eubiea Oriental','Yes','Yes'),
	array('Eubiea Occidental','Euboeius Sinus','Yes','No'),
	array('Eubiea Occidental','Thermas Sinus','Yes','No'),
	array('Eubiea Oriental','Euboeius Sinus','Yes','No'),
	array('Eubiea Oriental','Mare Aega','Yes','No'),
	array('Eubiea Oriental','Mare Mediterranea','Yes','No'),
	array('Eubiea Oriental','Thermas Sinus','Yes','No'),
	array('Euboeius Sinus','Marathon','Yes','No'),
	array('Euboeius Sinus','Mare Mediterranea','Yes','No'),
	array('Euboeius Sinus','Phthiotis','Yes','No'),
	array('Halieis','Iria','No','Yes'),
	array('Halieis','Iria (North Coast)','Yes','No'),
	array('Halieis','Iria (South Coast)','Yes','No'),
	array('Halieis','Saronieus Sinus','Yes','No'),
	array('Helicon','Opus','No','Yes'),
	array('Helicon','Phocis','Yes','Yes'),
	array('Helicon','Sinus Corinthiucus','Yes','No'),
	array('Helicon','Thebae','Yes','Yes'),
	array('Helos','Koidaunas','Yes','Yes'),
	array('Helos','Laconia','Yes','Yes'),
	array('Helos','Laconicus Sinus','Yes','No'),
	array('Helos','Prastos','No','Yes'),
	array('Helos','Sparta','No','Yes'),
	array('Ilia','Pisatis','Yes','Yes'),
	array('Ilia','Zazynthus Sinus','Yes','No'),
	array('Ionia','Lesbos','Yes','Yes'),
	array('Ionia','Marmora Sinus','Yes','No'),
	array('Ionia','Rhodius Sinus','Yes','No'),
	array('Ira','Khora','Yes','Yes'),
	array('Ira','Lycaion','No','Yes'),
	array('Ira','Megalopolis','No','Yes'),
	array('Ira','Messena','No','Yes'),
	array('Ira','Thuria','No','Yes'),
	array('Ira','Triphylia','Yes','Yes'),
	array('Iria (North Coast)','Saronieus Sinus','Yes','No'),
	array('Italus Sinus','Mare Adriaticum','Yes','No'),
	array('Italus Sinus','Rhegium','Yes','No'),
	array('Italus Sinus','Sicily','Yes','No'),
	array('Italus Sinus','Zazynthus Sinus','Yes','No'),
	array('Ithaca Sinus','Mare Adriaticum','Yes','No'),
	array('Ithaca Sinus','Nicopolicus Sinus','Yes','No'),
	array('Ithaca Sinus','Prian Rhium','Yes','No'),
	array('Ithaca Sinus','Zazynthus Sinus','Yes','No'),
	array('Kaphyae','Maniana','No','Yes'),
	array('Kaphyae','Orchenenus','No','Yes'),
	array('Kaphyae','Pellea','No','Yes'),
	array('Kaphyae','Protilae','No','Yes'),
	array('Khora','Messena','No','Yes'),
	array('Khora','Pylos','Yes','Yes'),
	array('Koidaunas','Laconicus Sinus','Yes','No'),
	array('Koidaunas','Prastos','Yes','Yes'),
	array('Laconia','Laconicus Sinus','Yes','No'),
	array('Laconia','Messeniacus Sinus','Yes','No'),
	array('Laconia','Sparta','No','Yes'),
	array('Laconia','Thuria','Yes','Yes'),
	array('Laconicus Sinus','Messeniacus Sinus','Yes','No'),
	array('Laconicus Sinus','Prastos','Yes','No'),
	array('Laconicus Sinus','Rhodius Sinus','Yes','No'),
	array('Lesbos','Marmora Sinus','Yes','No'),
	array('Locris','Phocis','Yes','Yes'),
	array('Locris','Prian Rhium','Yes','No'),
	array('Locris','Sinus Corinthiucus','Yes','No'),
	array('Lycaion','Megalopolis','No','Yes'),
	array('Lycaion','Pisatis','No','Yes'),
	array('Lycaion','Protilae','No','Yes'),
	array('Lycaion','Triphylia','No','Yes'),
	array('Maniana','Megalopolis','No','Yes'),
	array('Maniana','Orchenenus','No','Yes'),
	array('Maniana','Protilae','No','Yes'),
	array('Maniana','Tegea','No','Yes'),
	array('Marathon','Mare Mediterranea','Yes','No'),
	array('Mare Adriaticum','Nicopolicus Sinus','Yes','No'),
	array('Mare Adriaticum','Rhegium','Yes','No'),
	array('Mare Adriaticum','Tarentum','Yes','No'),
	array('Mare Aega','Mare Mediterranea','Yes','No'),
	array('Mare Aega','Marmora Sinus','Yes','No'),
	array('Mare Aega','Thermas Sinus','Yes','No'),
	array('Mare Mediterranea','Marmora Sinus','Yes','No'),
	array('Mare Mediterranea','Saronieus Sinus','Yes','No'),
	array('Marmora Sinus','Rhodius Sinus','Yes','No'),
	array('Megalopolis','Sparta','No','Yes'),
	array('Megalopolis','Tegea','No','Yes'),
	array('Megalopolis','Thuria','No','Yes'),
	array('Megara','Thebae','No','Yes'),
	array('Megara (East Coast)','Saronieus Sinus','Yes','No'),
	array('Megara (West Coast)','Sinus Corinthiucus','Yes','No'),
	array('Megara (West Coast)','Thebae','Yes','No'),
	array('Messena','Messeniacus Sinus','Yes','No'),
	array('Messena','Pylos','Yes','Yes'),
	array('Messena','Thuria','Yes','Yes'),
	array('Messeniacus Sinus','Pylos','Yes','No'),
	array('Messeniacus Sinus','Thuria','Yes','No'),
	array('Mycenae','Orchenenus','No','Yes'),
	array('Nicopolicus Sinus','Prian Rhium','Yes','No'),
	array('Opus','Phocis','No','Yes'),
	array('Opus','Thebae','No','Yes'),
	array('Orchenenus','Pellea','No','Yes'),
	array('Orchenenus','Tegea','No','Yes'),
	array('Patcae','Prian Rhium','Yes','No'),
	array('Pellea','Sinus Corinthiucus','Yes','No'),
	array('Phocis','Sinus Corinthiucus','Yes','No'),
	array('Phthiotis','Thermas Sinus','Yes','No'),
	array('Pisatis','Protilae','No','Yes'),
	array('Pisatis','Triphylia','Yes','Yes'),
	array('Pisatis','Zazynthus Sinus','Yes','No'),
	array('Prastos','Sparta','No','Yes'),
	array('Prian Rhium','Proschium','Yes','No'),
	array('Prian Rhium','Sinus Corinthiucus','Yes','No'),
	array('Prian Rhium','Zazynthus Sinus','Yes','No'),
	array('Proschium','Thermium','No','Yes'),
	array('Rhegium','Sicily','Yes','Yes'),
	array('Rhegium','Tarentum','Yes','Yes'),
	array('Rhodes','Rhodius Sinus','Yes','No'),
	array('Sinus Corinthiucus','Thebae','Yes','No'),
	array('Sparta','Tegea','No','Yes'),
	array('Sparta','Thuria','No','Yes')
);

foreach($bordersRawData as $borderRawRow)
{
	list($from, $to, $fleets, $armies)=$borderRawRow;
	InstallTerritory::$Territories[$to]  ->addBorder(InstallTerritory::$Territories[$from],$fleets,$armies);
}
unset($bordersRawData);

InstallTerritory::runSQL($this->mapID);
InstallCache::terrJSON($this->territoriesJSONFile(),$this->mapID);
?>