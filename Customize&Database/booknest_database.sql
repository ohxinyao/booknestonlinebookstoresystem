-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 12:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booknest_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sales` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `category`, `price`, `stock`, `image`, `rating_avg`, `rating_count`, `created_at`, `sales`, `min_stock`) VALUES
(1, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Harry Potter and the Sorcerer\'s Stone (1997) follows eleven-year-old orphan Harry Potter, who discovers he is a wizard and leaves his abusive aunt and uncle to attend Hogwarts School of Witchcraft and Wizardry. Alongside new friends Ron and Hermione, Harry navigates a magical world, uncovers his parents\' past, and battles Lord Voldemort', 'Fiction', 45.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1779273452_801.jpg', 0.00, 0, '2026-05-05 11:36:22', 20, 5),
(2, 'Warlord Chronicles #2: Enemy Of God', ' Bernard Cornwell', 'The balance of King Arthur\'s unified kingdom is threatened by Merlin\'s quest for the last of Britain\'s 13 Treasures; by the conflict between the ancient religion and the new Christianity; and by Britain\'s war with the Saxons. A master storyteller continues his retelling of the Arthurian legend.', 'Fiction', 30.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777982001_bookImage2.jpg', 5.00, 1, '2026-05-05 11:53:21', 15, 5),
(3, 'Dublin Murder Squad #3: Faithful Place', ' Tana French', 'The course of Frank Mackey\'s life was set by one defining moment when he was nineteen. The moment his girlfriend, Rosie Daly, failed to turn up for their rendezvous in Faithful Place, failed to run away with him to London as they had planned. Frank never heard from her again. Twenty years on, Frank is still in Dublin, working as an undercover cop. He\'s cut all ties with his dysfunctional family. Until his sister calls to say that Rosie\'s suitcase has been found. Frank embarks on a journey into his past that demands he reevaluate everything he believes to be true.', 'Fiction', 37.25, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777982672_1777982150_img.jpg', 5.00, 1, '2026-05-05 11:55:50', 10, 5),
(4, 'The Making Of Another Major Motion Picture Masterpiece', 'Tom Hanks', 'It is a wildly ambitious story of the making of a colossal, star-studded, multimillion-dollar superhero action film and the humble comic book that inspired it all. The making of another major motion picture masterpiece offers an insider\'s perspective on the significant efforts required to create a film, combining elements of humor, emotion, and thought-provoking insights. At once a reflection on America\'s past and present, on the world of show business, and on the real world we all live in.', 'Fiction', 25.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777993279_1777982680_1777982609_img2.jpg', 5.00, 1, '2026-05-05 12:03:29', 5, 5),
(5, 'The Little Liar', ' Mitch Albom', 'It is a historical fiction novel set during the Holocaust in Salonika, Greece. It follows Nico, a young boy renowned for his absolute honesty, who is tricked by a Nazi officer into lying to his Jewish community, urging them to board trains to their deaths. Haunted by this, Nico becomes a chronic liar, seeking atonement over decades', 'Fiction', 38.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777982863_img3.jpg', 0.00, 0, '2026-05-05 12:07:43', 0, 5),
(6, 'The 7 Habits Of Highly Effective People', ' Stephen R. Covey', 'One of the most inspiring and impactful books ever written, The 7 Habits of Highly Effective People has captivated readers for nearly three decades. It has transformed the lives of presidents and CEOs, educators and parents—millions of people of all ages and occupations', 'Non-fiction', 49.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777989585_img1.jpg', 0.00, 0, '2026-05-05 13:59:45', 0, 5),
(7, 'Talking To Strangers: What We Should Know About The People We Don\'t Know', ' Malcolm Gladwell', 'This book explores why humans are inept at understanding people they don\'t know, arguing that our misinterpretations of strangers\' intentions and behaviors often lead to societal conflict and tragedy.', 'Non-fiction', 42.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777989875_img2.jpg', 0.00, 0, '2026-05-05 14:04:35', 0, 5),
(8, 'The Power Of A Positive Attitude', 'Roger Fritz', 'This book shows a practical guide designed to help readers achieve personal and professional success by developing a resilient, optimistic mindset.', 'Non-fiction', 28.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777990124_img3.jpg', 0.00, 0, '2026-05-05 14:08:44', 0, 5),
(9, 'Stress-Free Productivity: A Personalised Toolkit to Become Your Most Efficient, Creative Self', 'Alice Boyes', 'This book shows a research-backed guide designed to help individuals build customized productivity systems that prioritize mental health. It moves away from one-size-fits-all advice, offering tools to manage perfectionism, cultivate self-compassion, and leverage unique personal quirks for efficient, creative work', 'Non-fiction', 25.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777990397_img4.jpg', 0.00, 0, '2026-05-05 14:13:17', 0, 5),
(10, 'Just One Thing: How simple changes can transform your life', 'Dr Michael Mosley', 'The book shows outlines small, scientifically backed lifestyle adjustments that, when implemented daily, can significantly improve mental and physical health. It focuses on easy, sustainable habits (like cold showers, singing, or walking) over grand resolutions.', 'Non-fiction', 31.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777991316_img5.jpg', 0.00, 0, '2026-05-05 14:28:36', 0, 5),
(11, '100 Facts Oceans', 'Clare Oliver', 'Take a deep breath and dive into an amazing watery world! Exactly 100 facts will help you discover everything you need to know about oceans. Learn about life beneath the waves, find out about underwater mountains and take a look at some crazy creatures of the deep.', 'Children', 15.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777992163_img1.jpg', 5.00, 1, '2026-05-05 14:42:02', 0, 5),
(12, 'A Child\'s Introduction to Space Exploration: An Explorer\'s Guide to Rockets, Astronauts, and Life in Zero Gravity', 'Michael E Bakich', 'This book includes interactive 90-page guide for ages 8–12, covering the history, science, and future of space travel. It features facts on rockets, astronaut life, STEM experiments, and profiles of key figures, illustrated with NASA photos.', 'Children', 35.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777992400_img2.jpg', 0.00, 0, '2026-05-05 14:46:40', 0, 5),
(13, 'A Friend To Nature', 'Laura Knowles', 'This book is a beautifully illustrated, hands-on guide for children aged 6–8. It encourages young readers to become eco-warriors through a \"friendship pledge\" and practical activities, such as building bird feeders and identifying local nature.', 'Children', 17.99, 49, '/finalproject/booknestonlinebookstoresystem/Image/1777992759_img3.jpg', 0.00, 0, '2026-05-05 14:52:39', 0, 5),
(14, 'Animal Explorers: Ivy The Bug Hunter', ' Sharon Rentta', 'A story about Ivy, an elephant who overcomes its dislike of bugs to become a passionate bug hunter. ', 'Children', 19.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777993047_img4.jpg', 0.00, 0, '2026-05-05 14:57:27', 0, 5),
(15, 'Bob\'s Blue Period', 'Marion Deuchars', 'A story about Bob the bird, an artist who becomes deeply sad and paints only in blue after his best friend, Bat, goes away. Through themes of friendship and emotional expression, Bob\'s friends help him navigate his loneliness and find joy in color again.', 'Children', 24.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1777993233_img5.jpg', 0.00, 0, '2026-05-05 15:00:33', 0, 5),
(16, 'Oxford Advanced Learner\'s Dictionary', 'Oxford', 'This book offers comprehensive definitions, example sentences, and tools for upper-intermediate to advanced learners (B2-C2), focusing on building vocabulary, improving pronunciation, and developing writing and speaking skills.', 'Education & Reference', 78.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778045690_img6.jpg', 0.00, 0, '2026-05-06 05:34:50', 0, 5),
(17, 'How to Win Friends and Influence People', 'Dale Carnegie', 'It is a timeless guide emphasizing that success stems from managing emotions, practicing empathy, and making others feel important. The core message is to stop criticizing, offer sincere appreciation, and view situations from the other person\'s perspective to influence them positively.', 'Education & Reference', 40.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778046388_img1.jpg', 0.00, 0, '2026-05-06 05:46:28', 0, 5),
(18, 'Thinking, Fast and Slow', 'Daniel Kahneman', 'This book explores two mental systems driving decisions: System 1 (fast, intuitive, emotional) and System 2 (slow, logical, effortful). Kahneman demonstrates that System 1 often leads to cognitive biases and errors, while System 2 is lazy and prone to allowing shortcuts, causing predictably irrational choices', 'Education & Reference', 46.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778046581_img2.jpg', 0.00, 0, '2026-05-06 05:49:41', 0, 5),
(19, 'Cambridge IELTS Academic 18', 'Cambridge', 'This book provides four authentic examination papers from Cambridge University Press & Assessment, offering the most realistic practice for the Academic module. ', 'Education & Reference', 70.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778047023_img3.jpg', 0.00, 0, '2026-05-06 05:57:03', 0, 5),
(20, 'How to Take Smart Notes', 'Sönke Ahrens', 'This book outlines the \"Zettelkasten\" (slip-box) method, a system for effective thinking, learning, and writing by connecting ideas rather than collecting information. It emphasizes creating permanent, atomized notes in your own words, linking them to existing notes, and organizing them bottom-up to develop ideas over time.', 'Education & Reference', 49.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778047708_img4.jpg', 0.00, 0, '2026-05-06 06:08:28', 0, 5),
(21, 'The Science of Why We Exist: A History of the Universe from the Big Bang to Consciousness', 'Tim Coulson ', 'It is an accessible scientific narrative tracing the 13.8-billion-year journey from the Big Bang to human consciousness. It explores the improbable chain of physical, chemical, and biological events necessary for existence, examining whether human life was inevitable or a result of extraordinary luck.', 'Science & Technology', 85.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778048585_img1.jpg', 0.00, 0, '2026-05-06 06:23:05', 0, 5),
(22, ' AI Valley: Microsoft, Google, and the Trillion-Dollar Race to Cash In on Artificial Intelligence', 'Gary Rivlin', 'This book is a journalistic account of the AI arms race, focusing on the personalities, venture capitalists, and tech giants (Google, Microsoft, Meta) competing to define the generative AI era.', 'Science & Technology', 90.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778048794_img2.jpg', 0.00, 0, '2026-05-06 06:26:34', 0, 5),
(23, 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'This book shows outlines principles for writing software that is easy to read, maintain, and extend. ', 'Science & Technology', 52.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778050285_img3.jpg', 0.00, 0, '2026-05-06 06:51:25', 0, 5),
(24, 'The Innovators: How a Group of Hackers, Geniuses, and Geeks Created the Digital Revolution', 'Walter Isaacson ', 'This book shows a comprehensive history of the digital revolution, highlighting that the computer and internet were created through collaboration, not just solo genius. ', 'Science & Technology', 78.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778051777_img4.jpg', 0.00, 0, '2026-05-06 07:16:17', 0, 5),
(25, 'Everything Is Predictable: How Bayesian Statistics Explain Our World ', 'Tom Chivers ', 'This book explores Bayes\' theorem which posits that humans intuitively update beliefs based on new data. Chivers argues this \"Bayesian brain\" approach explains how we make decisions, navigate uncertainty, and understand the world across fields like medicine, AI, law, and climate science', 'Science & Technology', 65.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778052370_img5.jpg', 0.00, 0, '2026-05-06 07:26:10', 0, 5),
(26, 'The Essentials Of Finance And Accounting For Nonfinancial Managers', 'Edward Fields', 'This book is a practical guide designed to help non-finance managers interpret financial data, understand annual reports, and make informed, profit-driven decisions.', 'Business & Finance', 23.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778054033_img1.jpg', 0.00, 0, '2026-05-06 07:53:53', 0, 5),
(27, 'Buy Low, Sell High : The Simplicity Of Business Finance', 'Philip Young', 'This book has a concise guide designed to demystify corporate finance for non-financial managers and entrepreneurs. It focuses on essential financial measures, teaching how businesses make money, how to read financial health, and how to improve performance.', 'Business & Finance', 23.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778054371_img2.jpg', 0.00, 0, '2026-05-06 07:59:31', 0, 5),
(28, 'Real Life Money: An Honest Guide to Taking Control of Your Finances', 'Clare Seal', 'This book is a compassionate, part-memoir guide focused on repairing one\'s relationship with money, tackling debt, and managing financial anxiety without sacrificing joy. It addresses the psychological, social, and practical causes of financial hardship.', 'Business & Finance', 20.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778054565_img3.jpg', 0.00, 0, '2026-05-06 08:02:45', 0, 5),
(29, 'Cloudmoney: Cash, Cards, Crypto, And The War For Our Wallets', 'Brett Scott', 'This book exposes the coordinated campaign by Big Finance and tech companies to eliminate physical cash in favor of digital \"cloudmoney.\" It argues that a cashless society removes privacy, creates financial exclusion, and transfers power to tech corporations.', 'Business & Finance', 26.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778054811_img4.jpg', 0.00, 0, '2026-05-06 08:06:51', 0, 5),
(30, 'Starting A Business From Home: Your Guide To Planning Your Home Start-Up', 'Colin Barrow', 'This book is a practical guide designed to help aspiring entrepreneurs plan, launch, and grow a business from home. It covers essential topics including market research, writing a business plan, raising capital, managing finances, building a website, and planning for expansion, offering actionable advice for creating a profitable enterprise.', 'Business & Finance', 48.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778055027_img5.jpg', 0.00, 0, '2026-05-06 08:10:27', 0, 5),
(31, 'Awakenings - A Guide To Living A Vegan Lifestyle', 'Lucy Watson', 'This book has a comprehensive, practical guide that demystifies veganism, showing it is a holistic lifestyle rather than just a diet. ', 'Lifestyle (Health, Cooking, Arts)', 25.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778056860_img6.png', 0.00, 0, '2026-05-06 08:41:00', 0, 5),
(32, 'Super Self-Care: How to Find Lasting Freedom from Addiction, Toxic Relationships and Dysfunctional Lifestyles', 'Christopher Dines', 'This book is a compassionate, practical guide for breaking free from addiction, toxic relationships, and dysfunctional behaviors. It emphasizes prioritizing mental, emotional, and spiritual well-being through mindfulness, self-compassion, and practical exercises, offering a roadmap to lasting recovery, inner peace, and authentic living.', 'Lifestyle (Health, Cooking, Arts)', 17.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778057238_img7.jpg', 0.00, 0, '2026-05-06 08:43:19', 0, 5),
(33, 'Clean Eating', 'Igloobooks', 'This book shows a nutritional approach focusing on consuming whole, minimally processed, and natural foods, such as fresh fruits, vegetables, lean proteins, and whole grains. Key principles include mindful eating, reading labels, and choosing nutrient-dense options.', 'Lifestyle (Health, Cooking, Arts)', 21.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778057855_img8.jpg', 0.00, 0, '2026-05-06 08:57:35', 0, 5),
(34, 'Green Living: A Comprehensive Guide to a Happy and Sustainable Life', 'Green Matters', 'This book is a practical, accessible guide offering actionable strategies to adopt an eco-friendly lifestyle. It covers waste reduction, sustainable fashion, non-toxic cleaning, and mindful consumption.', 'Lifestyle (Health, Cooking, Arts)', 40.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778064356_1778058293_img9.jpg', 0.00, 0, '2026-05-06 09:04:53', 0, 5),
(35, 'Keto Kitchen: Delicious recipes for energy and weight loss', 'Monya Kilian Palmer', 'It is a comprehensive cookbook designed to make the low-carb, high-fat ketogenic lifestyle accessible, flavorful, and sustainable. The book aims to support weight loss and improve mental clarity through easy-to-follow recipes tailored for busy schedules.', 'Lifestyle (Health, Cooking, Arts)', 43.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778064332_1778058504_img10.jpg', 0.00, 0, '2026-05-06 09:08:24', 0, 5),
(36, 'A Calamity Of Souls', 'David Baldacci', 'It is a 1968-set historical legal thriller about a Black Vietnam veteran, Jerome Washington, wrongfully accused of murdering a wealthy white couple in segregated Freeman County, Virginia. White lawyer Jack Lee and Black Chicago attorney Desiree DuBose team up to fight a corrupt system and save Washington from the electric chair, facing immense racial prejudice.', 'Fiction', 45.00, 49, '/finalproject/booknestonlinebookstoresystem/Image/1778136293_img1.jpg', 0.00, 0, '2026-05-07 06:44:53', 0, 5),
(37, 'Eruption', 'James Patterson', 'It is a techno-thriller by Michael Crichton and James Patterson, following a massive Mauna Loa eruption in Hawaii that threatens to expose a deadly, hidden Cold War-era chemical weapon. Scientists and military leaders race to avert a global ecological disaster as lava approaches the secret site.', 'Fiction', 39.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778137170_img2.jpg', 0.00, 0, '2026-05-07 06:59:30', 0, 5),
(38, 'The Grandest Game', 'Jennifer Lynn Barnes', 'It is a story about heiress Avery Grambs and the Hawthorne brothers host a $26 million, high-stakes competition on a private island. Seven contestants, including newcomer POV characters Lyra, Gigi, and Rohan, solve dangerous puzzles while harboring secrets.', 'Fiction', 38.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778138134_img3.jpg', 0.00, 0, '2026-05-07 07:15:34', 0, 5),
(39, 'One By One', ' Ruth Ware', 'It is a locked-room thriller where employees of a tech startup (\"Snoop\") are stranded by an avalanche at a luxurious French Alps ski chalet. Amidst tensions over a buyout, staff start dying one by one, shifting the story into a desperate battle for survival.', 'Fiction', 27.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778138718_img4.jpg', 0.00, 0, '2026-05-07 07:25:18', 0, 5),
(40, 'True Colours', 'Kristin Hannah', 'It is a dramatic novel focusing on the Grey sisters—Winona, Aurora, and Vivi Ann—on their family\'s Washington state horse ranch. Following their mother\'s death, jealousy, betrayal, and a shocking crime shatter their bond, testing loyalties, forgiveness, and family, particularly when the youngest sister\'s life implodes.', 'Fiction', 32.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778139659_img6.jpg', 0.00, 0, '2026-05-07 07:40:59', 0, 5),
(41, 'Generation Anxiety: A Millennial And Gen Z Guide To Staying Afloat In An Uncertain World', ' Dr Lauren Cook', 'This book shows a practical, evidence-based guide designed to help younger generations manage high anxiety levels caused by climate change, political instability, and financial pressures. It offers actionable tools, exercises, and personal insights to foster \"empowered acceptance\" in a chaotic world.', 'Non-fiction', 26.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778140421_img7.png', 0.00, 0, '2026-05-07 07:53:41', 0, 5),
(42, 'Conflict Resilience: Negotiating Disagreement Without Giving Up Or Giving In', 'Robert Bordone', 'This book provides a research-backed framework for managing conflict by building the capacity to sit with discomfort, fostering deeper relationships rather than fleeing from or forcing resolution. It combines negotiation expertise with behavioral neurology to offer practical tools for navigating disagreements with integrity.', 'Non-fiction', 55.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778140930_img8.png', 0.00, 0, '2026-05-07 08:02:11', 0, 5),
(43, 'Make Your Bed: Little Things That Can Change Your Life...And Maybe The World', 'Admiral William H. Mcraven', 'It is a motivational book based on his viral 2014 commencement speech. It offers 10 actionable lessons from Navy SEAL training, emphasizing that small, disciplined tasks—starting with making your bed—create positive habits, resilience, and a \"ripple effect\" to overcome challenges and achieve big goals', 'Non-fiction', 45.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778141432_img9.png', 0.00, 0, '2026-05-07 08:10:32', 0, 5),
(44, 'Atomic Habits', 'James Clear', 'This book provides a framework for improving every day by focusing on tiny, incremental changes (1% better) rather than massive, overnight transformations. The core philosophy is that habits are the \"compound interest of self-improvement,\" where small, consistent actions (systems) compound over time to create, remarkable, long-term results.', 'Non-fiction', 30.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778141998_img2.jpg', 0.00, 0, '2026-05-07 08:19:58', 0, 5),
(45, 'The Power Of Your Subconscious Mind', 'Joseph Murphy', 'This is a classic self-help book that teaches how to harness the subconscious mind to achieve success, health, and happiness. It highlights that by changing one’s inner thought patterns—through visualization, affirmation, and belief—one can positively influence their outer physical reality, relationships, and financial status.', 'Non-fiction', 25.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778142277_img3.jpg', 0.00, 0, '2026-05-07 08:24:37', 0, 5),
(46, '100 Facts World Wonders', 'Adam Hibbert', 'It is a 48-page illustrated educational book for children (aged 7+) that presents 100 numbered, bite-sized facts about famous natural and man-made landmarks. It covers sites like ancient temples and modern skyscrapers, featuring detailed photos, cartoons, quizzes, and projects.', 'Children', 19.00, 47, '/finalproject/booknestonlinebookstoresystem/Image/1778142557_img4.jpg', 0.00, 0, '2026-05-07 08:29:17', 0, 5),
(47, 'Aliens and Other Worlds: True Tales from Our Solar System and Beyond', 'Lisa Harvey-Smith', ' it is an engaging, illustrated children book exploring the search for extraterrestrial life. It examines where to find aliens, what they might look like, and if they live among us.', 'Children', 28.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778142850_img5.jpg', 0.00, 0, '2026-05-07 08:34:10', 0, 5),
(48, 'All The Animals Were Sleeping', 'Clare Helen Welsh', 'It is a soothing bedtime picture book featuring a little mongoose traversing the Serengeti as night falls. As he heads home, he observes various animals sleeping in unique ways, offering a gentle, rhythmic, and informative tale about nature and rest.', 'Children', 14.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778143050_img6.jpg', 0.00, 0, '2026-05-07 08:37:30', 0, 5),
(49, 'Be Yourself: Why It\'s Great to Be You', 'Poppy O\'Neill', 'It is a practical, engaging guide designed for children aged 7–11 to foster self-acceptance, build confidence, and embrace individuality. Featuring a supportive character named Glow, the book uses cognitive behavioral therapy (CBT) and mindfulness techniques to help children manage negative thoughts and peer pressure.', 'Children', 27.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778143252_img7.jpg', 0.00, 0, '2026-05-07 08:40:52', 0, 5),
(50, 'Fluffles: The Brave Koala Who Held Strong Through a Bushfire', 'Vita Murrow', 'It is a heartwarming, true-based children\'s picture book about a koala surviving the 2020 Australian bushfires. It follows Fluffles as she escapes flames by climbing to the top of a tree, gets rescued with burnt paws, and heals by snuggling with other koalas.', 'Children', 26.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778143466_img8.jpg', 0.00, 0, '2026-05-07 08:44:26', 0, 5),
(51, 'The Merriam-Webster Dictionary', 'Merriam-Webster', 'It is a premier, authoritative American English reference, featuring over 75,000 clear, concise definitions, 8,000+ usage examples, and extensive word histories. Updated regularly to include new vocabulary across science, technology, and culture, it serves as a reliable guide for spelling, pronunciation, and synonyms.', 'Education & Reference', 65.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778144442_img9.jpg', 0.00, 0, '2026-05-07 09:00:01', 0, 5),
(52, 'How to win at College: Simple Rules for Success From Star Students', 'Cal Newport ', 'This book is a guide offering 75 actionable, unconventional strategies to excel academically and socially without burning out. Based on interviews with successful students, it emphasizes working smarter, building a distinct identity, and enjoying the college experience.', 'Education & Reference', 72.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778144736_img10.jpg', 0.00, 0, '2026-05-07 09:05:36', 0, 5),
(53, 'The Elements of Style - Illustrated', 'William Strunk Jr. & E.B. White', 'This book is a classic writing guide, featuring 57 whimsical, colorful illustrations by Maira Kalman. It combines the original, authoritative rules on grammar, composition, and style with a vibrant visual interpretation, making the instructional content more engaging and accessible.', 'Education & Reference', 65.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778145748_img11.jpg', 0.00, 0, '2026-05-07 09:22:28', 0, 5),
(54, 'The Study Skills Handbook', 'Stella Cottrell ', 'This book is a comprehensive, practical guide designed to help university students optimize their learning, build confidence, and boost employability. It offers tailored strategies for time management, critical thinking, academic writing, and note-making.', 'Education & Reference', 78.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778146548_img12.jpg', 0.00, 0, '2026-05-07 09:35:48', 0, 5),
(55, 'A Manual for Writers of Research Papers', 'Kate L. Turabian', 'This book is the definitive, comprehensive guide for students and researchers on crafting, formatting, and citing academic papers. It provides a three-part framework covering the research process, citation styles (notes-bibliography and author-date), and editorial style, aligning with Chicago Manual of Style.', 'Education & Reference', 80.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778147150_img13.jpg', 0.00, 0, '2026-05-07 09:45:50', 0, 5),
(56, 'The Doomsday Book: The Science Behind Humanity\'s Greatest Threats', 'Marshall Brain', 'This book is an illustrated 288-page exploration of potential existential risks to human civilization. It examines natural, manmade, and science-fiction scenarios—such as pandemics, nuclear war, AI, and asteroid impacts—providing scientific explanations, impact analysis, and potential mitigation strategies.', 'Science & Technology', 30.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778149061_img14.jpg', 0.00, 0, '2026-05-07 10:17:41', 0, 5),
(57, 'The Big Ideas in Science: A complete introduction', 'Jon Evans', 'This book is an accessible guide, part of the Teach Yourself series, providing a comprehensive overview of fundamental scientific concepts. Covering topics from the Big Bang to modern technology, it explains key ideas in physics, biology, chemistry, and environmental science for a general audience.', 'Science & Technology', 21.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778149850_img15.jpg', 0.00, 0, '2026-05-07 10:30:50', 0, 5),
(58, 'Wild Weather: The Myths, Science & Wonder of Weather', 'Alison Davies', 'This book is a beautifully illustrated, 144-page guide exploring meteorological phenomena, folklore, and myth. It explains how weather events like rain, wind, and lightning occur, while offering a fun, accessible approach to connecting with nature and embracing different weather conditions.', 'Science & Technology', 23.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778150675_img16.jpg', 0.00, 0, '2026-05-07 10:44:35', 0, 5),
(59, 'Youniverse: A Short Guide to Modern Science', 'Elsie Burch Donald', 'This book is an accessible, 240-page primer explaining fundamental scientific concepts from the Big Bang to AI. It explores humanity\'s place in the universe using plain language, short chapters, and minimal jargon, focusing on topics like matter, energy, and evolution, all vetted by experts.', 'Science & Technology', 24.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778150913_img17.jpg', 0.00, 0, '2026-05-07 10:48:33', 0, 5),
(60, 'Seeing Science: The Art Of Making The Invisible Visible', 'Jack Challoner', 'This book is a visually driven exploration of how scientists use imaging technologies to make hidden, abstract, or microscopic phenomena tangible. With over 200 color images, it explores how we visualize everything from atomic structures to cosmic events.', 'Science & Technology', 22.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778151255_img19.jpg', 0.00, 0, '2026-05-07 10:53:47', 0, 5),
(61, 'Starting A Successful Business: Your Guide To Setting Up Your Dream Start-Up, Controlling Its Finances And Managing Its Operations (Business Success)', 'Michael J. Morris', 'This book offers a practical guide to turning business ideas into profitable, long-term ventures. It provides essential advice on planning, marketing, and controlling finances, helping entrepreneurs avoid pitfalls in the crucial first 18 months.', 'Business & Finance', 40.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778159621_img1.jpg', 0.00, 0, '2026-05-07 13:13:41', 0, 5),
(62, 'From Monk To Money Manager: A Former Monk\'s Financial Guide To Becoming A Little Bit Wealthy - And Why That\'s Okay', 'Doug Lynam', 'This book is a financial guide blending spiritual wisdom with practical investment advice, arguing that building moderate wealth is a moral, empowering act. Lynam, a former Benedictine monk turned financial advisor, advocates for mindful, long-term, low-cost investing to achieve financial freedom and better help others.', 'Business & Finance', 18.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778160592_img2.jpg', 0.00, 0, '2026-05-07 13:23:18', 0, 5),
(63, 'Financially Forward: How To Use Today\'S Digital Tools To Earn More, Save Better, And Spend Smarter', 'Alexa Von Tobel', 'This book provides a straightforward guide to optimizing personal finances using modern technology. It provides actionable advice on leveraging smartphone apps, automation, and digital banking to manage money more efficiently, aiming to increase savings and improve spending habits in a digital economy.', 'Business & Finance', 26.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778160573_img3.jpg', 0.00, 0, '2026-05-07 13:29:22', 0, 5),
(64, 'How to Write a Business Plan: Win Backing and Support for Your Ideas and Ventures', 'Brian Finch', 'This book provides a practical guide for entrepreneurs to create compelling plans that win investor backing. The 7th edition emphasizes using a 15–25 page format (or 1-page summary) packed with realistic data, market analysis, and clear financial projections to prove viability, specifically addressing the key questions potential backers ask.', 'Business & Finance', 22.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778160873_img4.jpg', 0.00, 0, '2026-05-07 13:34:33', 0, 5),
(65, 'Anthro-Vision: A New Way To See In Business And Life', 'Gillian Tett', 'This book argues that applying anthropological methods—empathy, observation, and \"making the familiar strange\"—helps leaders navigate a complex world better than relying solely on big data or economic models. Tett demonstrates how understanding human culture uncovers hidden behaviors and drives better innovation.', 'Business & Finance', 29.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778161291_img5.jpg', 0.00, 0, '2026-05-07 13:41:31', 0, 5),
(66, 'The Lazarus Strategy: How To Age Well And Wisely', ' Norman Lazarus', 'This book is a \"part how-to, part manifesto\" that challenges the idea that aging must mean inevitable physical and mental decline. Dr. Lazarus, an 84-year-old expert in exercise physiology, uses his own active, medication-free life as proof that later years can be vibrant and productive.', 'Lifestyle (Health, Cooking, Arts)', 20.50, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778162554_img6.jpg', 0.00, 0, '2026-05-07 14:02:34', 0, 5),
(67, 'Real Food By Mike', 'Mike McEnearney', 'It is a seasonal cookbook focusing on wholefoods, wellbeing, and the concept of a \"physic garden\". It offers fresh, delicious recipes designed to improve long-term health while celebrating nutritious, enjoyable eating through dishes that are both indulgent and healthy.', 'Lifestyle (Health, Cooking, Arts)', 32.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778162999_img7.jpg', 0.00, 0, '2026-05-07 14:09:59', 0, 5),
(68, 'Art Of Blending', 'Tori Ritchie', 'It  is a versatile cookbook designed to help owners of high-performance blenders (specifically the Vitamix Professional Series) move beyond basic smoothies. The book positions the pro-blender as a multi-functional tool capable of acting as a food processor, ice cream maker, and even a stove for heating soups.', 'Lifestyle (Health, Cooking, Arts)', 26.90, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778163410_img8.jpg', 0.00, 0, '2026-05-07 14:16:50', 0, 5),
(69, 'The Art Of Healthy Food - Gluten Free', 'Jasmin Peppiatt', 'It is a 256-page cookbook designed to help readers boost energy and lose weight by adopting a gluten-free lifestyle. It provides practical information for reducing or eliminating gluten, featuring various recipes aimed at making healthy, gluten-free eating accessible and enjoyable.', 'Lifestyle (Health, Cooking, Arts)', 23.00, 50, '/finalproject/booknestonlinebookstoresystem/Image/1778163687_img9.jpg', 0.00, 0, '2026-05-07 14:21:27', 0, 5),
(70, '[Bargain Corner] The Art Of Healthy Food - Dairy Free', 'Jasmin Peppiatt', 'It is a paperback cookbook featuring dairy-free recipes, designed for those with allergies, intolerances, or seeking healthier lifestyle alternatives. It is part of a series focusing on easy, nutritious meals with inspirational imagery, often sold at discounted prices.', 'Lifestyle (Health, Cooking, Arts)', 16.00, 49, '/finalproject/booknestonlinebookstoresystem/Image/1778163928_img10.jpg', 0.00, 0, '2026-05-07 14:25:28', 0, 5),
(71, 'The Invisible Life Of Addie Larue', 'V. E. Schwab', 'The story is about a young French woman who makes a Faustian bargain for immortality in 1714. Her curse strips her of all permanence: she cannot leave physical marks, and everyone she meets forgets her the moment they look away. After nearly 300 years of lonely wandering, she meets a Manhattan bookseller who miraculously remembers her.', 'Fiction', 42.00, 49, '/finalproject/booknestonlinebookstoresystem/Image/1779279460_871.jpg', 0.00, 0, '2026-05-19 08:33:19', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Fiction', '2026-06-10 08:28:37'),
(2, 'Non-fiction', '2026-06-10 08:28:37'),
(3, 'Children', '2026-06-10 08:28:37'),
(4, 'Education & Reference', '2026-06-10 08:28:37'),
(5, 'Science & Technology', '2026-06-10 08:28:37'),
(6, 'Business & Finance', '2026-06-10 08:28:37'),
(7, 'Lifestyle (Health, Cooking, Arts)', '2026-06-10 08:28:37');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `is_read`) VALUES
(1, 1, 3, 'hello????', '2026-05-27 09:01:14', 1),
(2, 1, 3, 'hello????', '2026-05-27 09:01:22', 1),
(3, 1, 14, 'hello', '2026-05-27 09:02:18', 1),
(4, 14, 1, 'hello how can i help you', '2026-05-27 09:03:33', 1),
(5, 1, 14, 'may i know the status of my order', '2026-05-28 07:07:19', 1),
(6, 3, 1, 'hi how can i help you', '2026-05-28 08:05:48', 1),
(7, 3, 1, 'hello', '2026-05-28 08:06:02', 1),
(8, 3, 1, 'hello', '2026-05-28 08:06:57', 1),
(9, 3, 1, '..', '2026-05-28 09:15:29', 1),
(10, 1, 3, '..', '2026-05-28 09:17:20', 0),
(11, 1, 14, '。。', '2026-05-28 10:14:49', 1),
(12, 1, 14, '.', '2026-05-28 10:25:53', 1),
(13, 1, 14, '.', '2026-05-28 10:25:57', 1),
(14, 1, 14, '.', '2026-05-28 10:26:01', 1),
(15, 1, 14, '.', '2026-05-28 10:26:04', 1),
(16, 1, 14, '.', '2026-05-28 10:26:06', 1),
(17, 1, 14, '.', '2026-05-28 10:26:11', 1),
(18, 1, 14, '.', '2026-05-28 10:37:57', 1),
(19, 1, 14, '.', '2026-05-28 10:38:02', 1),
(20, 1, 14, '.', '2026-05-28 10:38:06', 1),
(21, 1, 14, '.', '2026-05-28 10:38:14', 1),
(22, 1, 14, 'hello', '2026-05-28 10:41:28', 1),
(23, 14, 1, 'gg', '2026-05-28 10:44:45', 1),
(24, 14, 1, 'bv', '2026-05-28 10:44:49', 1),
(25, 1, 3, 'hello', '2026-05-28 11:18:17', 0),
(26, 1, 3, '。', '2026-05-28 11:41:42', 0),
(27, 1, 3, '。。', '2026-05-28 11:41:47', 0),
(28, 1, 3, 'd', '2026-05-28 11:41:58', 0),
(29, 1, 3, 'd', '2026-05-28 11:42:03', 0),
(30, 1, 3, 'sds', '2026-05-28 11:53:46', 0),
(31, 1, 3, 'xsxs', '2026-05-28 11:53:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `order_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 2, 'Your order #ORD1779075427419 status is now shipped. It was shipped on 20 May 2026.', 1, '2026-05-20 09:31:42'),
(2, 1, 2, 'Your order #ORD1779075427419 status is now shipped. It was shipped on 20 May 2026.', 1, '2026-05-20 09:41:50'),
(3, 1, 2, 'Your order #ORD1779075427419 status is now shipped. It was shipped on 20 May 2026.', 1, '2026-05-20 09:44:53'),
(4, 1, 2, 'Your order #ORD1779075427419 status is now completed.', 1, '2026-05-20 10:26:08'),
(5, 1, 2, 'Your order #ORD1779075427419 status is now shipped. It was shipped on 21 May 2026.', 1, '2026-05-21 02:56:30'),
(6, 1, 6, 'Your order #ORD1779523417671 status is now paid.', 1, '2026-05-23 12:32:33'),
(7, 1, 6, 'Your order #ORD1779523417671 status is now processing.', 1, '2026-05-23 12:32:42'),
(8, 1, 5, 'Your order #ORD1779523115817 status is now pending.', 1, '2026-05-23 12:59:25'),
(9, 1, 5, 'Your order #ORD1779523115817 has been cancelled as requested.', 1, '2026-05-23 12:59:49'),
(10, 1, 6, 'Your order #ORD1779523417671 status is now completed.', 1, '2026-05-23 14:20:03'),
(11, 1, 7, 'Your order #ORD1779617229844 status is now processing.', 1, '2026-05-25 02:38:11'),
(12, 1, 4, 'Your order #ORD1779332596597 status is now processing.', 1, '2026-05-25 02:38:24'),
(13, 1, 8, 'Your order #ORD1779676813634 has been cancelled as requested.', 1, '2026-05-25 02:40:46'),
(14, 1, 8, 'Your order #ORD1779676813634 status is now pending.', 1, '2026-05-26 08:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_proof` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `voucher_code` varchar(50) DEFAULT NULL,
  `cancel_requested` tinyint(1) DEFAULT 0,
  `shipped_date` datetime DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `payment_status`, `payment_proof`, `order_date`, `discount_amount`, `voucher_code`, `cancel_requested`, `shipped_date`, `cancellation_reason`) VALUES
(1, 1, 'ORD1779075170770', 122.39, 'cancelled', 'paid', '1779075344_WhatsApp Image 2026-05-17 at 15.25.26.jpeg', '2026-05-18 03:32:50', 30.60, 'BOOKNEST20', 0, NULL, 'Wrong amount transfer'),
(2, 1, 'ORD1779075427419', 45.00, 'shipped', 'paid', '1779075441_WhatsApp Image 2026-05-17 at 15.25.26.jpeg', '2026-05-18 03:37:07', 0.00, NULL, 0, '2026-05-21 04:56:24', NULL),
(3, 1, 'ORD1779332319977', 37.25, 'cancelled', 'paid', '1779332340_WhatsApp Image 2026-05-17 at 15.25.26.jpeg', '2026-05-21 02:58:39', 0.00, NULL, 0, NULL, 'Payment not received'),
(4, 1, 'ORD1779332596597', 25.00, 'processing', 'paid', '1779332608_WhatsApp Image 2026-05-17 at 15.25.26.jpeg', '2026-05-21 03:03:16', 0.00, NULL, 0, NULL, NULL),
(5, 1, 'ORD1779523115817', 12.00, 'cancelled', 'unpaid', NULL, '2026-05-23 07:58:35', 3.00, 'BOOKNEST20', 0, NULL, NULL),
(6, 1, 'ORD1779523417671', 42.00, 'completed', 'unpaid', NULL, '2026-05-23 08:03:37', 0.00, NULL, 0, NULL, NULL),
(7, 1, 'ORD1779617229844', 35.00, 'processing', 'unpaid', NULL, '2026-05-24 10:07:09', 0.00, NULL, 0, NULL, NULL),
(8, 1, 'ORD1779676813634', 38.00, 'pending', 'unpaid', NULL, '2026-05-25 02:40:13', 0.00, NULL, 0, NULL, NULL),
(9, 1, 'ORD1780136332346', 16.19, 'pending', 'unpaid', NULL, '2026-05-30 10:18:52', 1.80, 'SAVE10', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(14, 1, 1, 1, 45.00),
(15, 1, 13, 1, 17.99),
(16, 1, 36, 2, 45.00),
(17, 2, 36, 1, 45.00),
(18, 3, 3, 1, 37.25),
(19, 4, 4, 1, 25.00),
(20, 5, 11, 1, 15.00),
(21, 6, 71, 1, 42.00),
(22, 7, 46, 1, 19.00),
(23, 7, 70, 1, 16.00),
(24, 8, 46, 2, 19.00),
(25, 9, 13, 1, 17.99);

-- --------------------------------------------------------

--
-- Table structure for table `password_change_requests`
--

CREATE TABLE `password_change_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_change_requests`
--

INSERT INTO `password_change_requests` (`id`, `user_id`, `requested_at`, `status`, `approved_by`, `approved_at`, `token`) VALUES
(1, 4, '2026-05-13 09:15:02', 'approved', 2, '2026-05-13 09:15:28', '6aa75a428da660ef17f0d5381a2a62791d29b60c4b967d49300691b4f37a8e73'),
(3, 1, '2026-05-14 09:43:40', 'approved', 2, '2026-05-14 09:45:12', 'a196c311dcd4d384f551f9b79839e93ae8f062df22f71e7f9ad46991c201eb6f'),
(4, 14, '2026-05-14 10:00:10', 'approved', 2, '2026-05-14 10:00:38', 'b3e6939f0cf8cec3b39d6d319387f6aa4215fd9602a600f11d4497fb4a36edbe');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `book_id`, `user_id`, `rating`, `comment`, `created_at`, `is_anonymous`) VALUES
(9, 11, 1, 5, 'hello', '2026-05-27 08:57:02', 1),
(12, 2, 1, 5, 'Hello', '2026-06-03 06:42:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','staff','admin') NOT NULL DEFAULT 'customer',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `must_change_password` tinyint(1) DEFAULT 0,
  `last_activity` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `role`, `email_verified`, `verification_token`, `reset_token`, `reset_expires`, `created_at`, `must_change_password`, `last_activity`) VALUES
(1, 'PHANG YU XUE', 'phangyuxue@gmail.com', NULL, NULL, '$2y$10$RuqGp4TC7nlwgKQQwodVrOcovQrpTGEABw59Kj3WXVXPY3V.Enq9G', 'customer', 1, NULL, NULL, NULL, '2026-05-03 15:01:56', 0, '2026-06-11 14:12:30'),
(2, 'Admin', 'phangyuxue@graduate.utm.my', NULL, NULL, '$2y$10$aLIeo2UrbwoBUYFh8L8nX.hTGs/QwVmRodYHtRDNZZ4VpbLxjcJmm', 'admin', 1, NULL, NULL, NULL, '2026-05-05 08:13:20', 0, '2026-06-11 12:10:06'),
(3, 'Staff_1', 'staff123@gmail.com', NULL, NULL, '$2y$10$kt/IDtTosIsUrF.h99tW8uVKUe.TDq4xvDVnRoh9YNd8dprhWTI.2', 'staff', 1, NULL, '10d38dadc75bcc7332b8586fe57cabb2c07b60cae86e2002156a3cb6363f52fe', '2026-05-07 12:41:40', '2026-05-05 13:44:38', 0, '2026-05-28 09:15:32'),
(4, 'Staff_2', 'staff456@gmail.com', NULL, NULL, '$2y$10$DdTE0yaqQPQdyxBM40vwgOcSp.A.w6J8iBrqN21N.YHRLhH7waiDe', 'staff', 1, NULL, NULL, NULL, '2026-05-08 10:11:34', 0, NULL),
(5, 'Staff_3', 'staff678@gmail.com', NULL, NULL, '$2y$10$v5.Ok9pAN/RDaSWgMY.fSOJJyZ..bBcu4L5CuXW9BXDGogD10AWdi', 'staff', 1, NULL, NULL, NULL, '2026-05-08 14:13:46', 0, NULL),
(6, 'Staff_4', 'staff789@gmail.com', NULL, NULL, '$2y$10$58GcpC6Mxnjbarazeum/lOnd76cbyjOGGOBXtH53O/Lqp18YGqVRy', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:25:45', 0, NULL),
(7, 'Staff_5', 'staff246@gmail.com', NULL, NULL, '$2y$10$eHDpq2dwQ4rAm0PG.c9uL.RhmH7sndcNZ9e60bPMP5ombmF8MvrNe', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:26:26', 0, NULL),
(8, 'Staff_6', 'staff135@gmail.com', NULL, NULL, '$2y$10$C.ryjfPLs7aLLzwStLeZ/eCO2mhUCIdvGcT19hmwfkuXMkWrkZ/nO', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:29:09', 0, NULL),
(9, 'Staff_7', 'staff234@gmail.com', NULL, NULL, '$2y$10$xTrrmr1XSksGOvSEHVsJ5Ozim5SERlmlCxOI12hPViTxsrk86Wzl2', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:29:30', 0, NULL),
(10, 'Staff_8', 'staff579@gmail.com', NULL, NULL, '$2y$10$YKDydq1h2RG86S8lVvE0x.Gq6Q7/aOEHQo5gBf4yGD288a9dy2Ek2', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:30:00', 0, NULL),
(11, 'Staff_9', 'staff567@gmail.com', NULL, NULL, '$2y$10$cFm/cU3y1ugLFamd5.kNFe4PhsdDoiXSPSzcogZb.BT9MgZOdY.HG', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:30:42', 0, NULL),
(12, 'Staff_10', 'staff357@gmail.com', NULL, NULL, '$2y$10$blN/q6q2be/UjL1Km/DyhO6lM2tZB3ycWn4kHdxmRG5gIfD2h.Uq.', 'staff', 1, NULL, NULL, NULL, '2026-05-09 09:31:25', 0, NULL),
(14, 'Staff_11', 'projectfinal432@gmail.com', NULL, NULL, '$2y$10$fH5wdJQvG9ddQk/sQku5Z.uXH019QA9YFNDTZeZ61bVlkgcZw0f2C', 'staff', 1, NULL, NULL, NULL, '2026-05-14 03:34:51', 0, '2026-06-14 10:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_cart`
--

CREATE TABLE `user_cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cart`
--

INSERT INTO `user_cart` (`id`, `user_id`, `book_id`, `quantity`, `added_at`) VALUES
(31, 1, 1, 1, '2026-06-11 13:56:23'),
(32, 1, 11, 1, '2026-06-11 13:56:23'),
(34, 1, 36, 1, '2026-06-11 13:57:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_wishlist`
--

CREATE TABLE `user_wishlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_wishlist`
--

INSERT INTO `user_wishlist` (`id`, `user_id`, `book_id`, `added_at`) VALUES
(1, 1, 4, '2026-05-30 08:04:20');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `valid_from` datetime DEFAULT current_timestamp(),
  `valid_to` datetime DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `conditions` text DEFAULT NULL COMMENT 'JSON encoded conditions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `valid_from`, `valid_to`, `usage_limit`, `used_count`, `active`, `conditions`) VALUES
(1, 'BOOKNEST20', 'percentage', 20.00, 0.00, '2026-05-15 13:41:27', '2026-06-30 23:59:59', NULL, 5, 1, '{\"categories\": [\"Fiction\"]}'),
(2, 'WELCOME10', 'percentage', 10.00, 0.00, '2026-05-15 13:41:27', '2026-06-30 23:59:59', NULL, 0, 1, '{\"new_member_only\": true}'),
(3, 'SAVE10', 'percentage', 10.00, 0.00, '2026-05-15 15:03:45', '2026-06-30 23:59:59', NULL, 1, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_participants` (`sender_id`,`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_order_date` (`order_date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `password_change_requests`
--
ALTER TABLE `password_change_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`book_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_cart`
--
ALTER TABLE `user_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_book` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `user_wishlist`
--
ALTER TABLE `user_wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_book` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `password_change_requests`
--
ALTER TABLE `password_change_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_cart`
--
ALTER TABLE `user_cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `user_wishlist`
--
ALTER TABLE `user_wishlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_change_requests`
--
ALTER TABLE `password_change_requests`
  ADD CONSTRAINT `password_change_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_cart`
--
ALTER TABLE `user_cart`
  ADD CONSTRAINT `user_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cart_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_wishlist`
--
ALTER TABLE `user_wishlist`
  ADD CONSTRAINT `user_wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_wishlist_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
