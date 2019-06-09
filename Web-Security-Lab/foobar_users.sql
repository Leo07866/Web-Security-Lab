CREATE TABLE IF NOT EXISTS `foobar_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email_address` varchar(50) NOT NULL
);

INSERT INTO `foobar_users` (`id`, `username`, `password`, `first_name`, `surname`, `email_address`) VALUES
(1, 'foo', 'password@124', 'Foo', 'User', 'foo.user@example.com'),
(2, 'admin', 'password@123', 'Admin', 'User', 'admin.user@example.com');

ALTER TABLE `foobar_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `foobar_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;