SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `wtab__users` (
  `id_user` int(11) NOT NULL,
  `wtab_email_and_user` varchar(50) NOT NULL,
  `wtab_psw` varchar(255) NOT NULL,
  `wtab_name` varchar(50) NOT NULL,
  `wtab_surname` varchar(50) NOT NULL,
  `wtab_usertype_id` int(11) NOT NULL,
  `insert` datetime NOT NULL DEFAULT current_timestamp(),
  `update` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `wtab__users` (`id_user`, `wtab_email_and_user`, `wtab_psw`, `wtab_name`, `wtab_surname`, `wtab_usertype_id`, `insert`, `update`) VALUES
(1, 'admin-test', '', 'Me', '', 1, '2018-02-20 13:54:21', '2021-03-06 13:57:17');

ALTER TABLE `wtab__users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `wtab_email_and_user` (`wtab_email_and_user`);

ALTER TABLE `wtab__users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
