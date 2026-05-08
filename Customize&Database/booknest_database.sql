-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 08:10 AM
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
  `sales` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `category`, `price`, `stock`, `image`, `rating_avg`, `rating_count`, `created_at`, `sales`) VALUES
(1, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Harry Potter and the Sorcerer\'s Stone (1997) follows eleven-year-old orphan Harry Potter, who discovers he is a wizard and leaves his abusive aunt and uncle to attend Hogwarts School of Witchcraft and Wizardry. Alongside new friends Ron and Hermione, Harry navigates a magical world, uncovers his parents\' past, and battles Lord Voldemort', 'Fiction', 45.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777982016_bookImage1.jpg', 0.00, 0, '2026-05-05 11:36:22', 20),
(2, 'Warlord Chronicles #2: Enemy Of God', ' Bernard Cornwell', 'The balance of King Arthur\'s unified kingdom is threatened by Merlin\'s quest for the last of Britain\'s 13 Treasures; by the conflict between the ancient religion and the new Christianity; and by Britain\'s war with the Saxons. A master storyteller continues his retelling of the Arthurian legend.', 'Fiction', 30.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777982001_bookImage2.jpg', 0.00, 0, '2026-05-05 11:53:21', 15),
(3, 'Dublin Murder Squad #3: Faithful Place', ' Tana French', 'The course of Frank Mackey\'s life was set by one defining moment when he was nineteen. The moment his girlfriend, Rosie Daly, failed to turn up for their rendezvous in Faithful Place, failed to run away with him to London as they had planned. Frank never heard from her again. Twenty years on, Frank is still in Dublin, working as an undercover cop. He\'s cut all ties with his dysfunctional family. Until his sister calls to say that Rosie\'s suitcase has been found. Frank embarks on a journey into his past that demands he reevaluate everything he believes to be true.', 'Fiction', 37.25, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777982672_1777982150_img.jpg', 0.00, 0, '2026-05-05 11:55:50', 10),
(4, 'The Making Of Another Major Motion Picture Masterpiece', 'Tom Hanks', 'It is a wildly ambitious story of the making of a colossal, star-studded, multimillion-dollar superhero action film and the humble comic book that inspired it all. The making of another major motion picture masterpiece offers an insider\'s perspective on the significant efforts required to create a film, combining elements of humor, emotion, and thought-provoking insights. At once a reflection on America\'s past and present, on the world of show business, and on the real world we all live in.', 'Fiction', 25.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777993279_1777982680_1777982609_img2.jpg', 0.00, 0, '2026-05-05 12:03:29', 5),
(5, 'The Little Liar', ' Mitch Albom', 'It is a historical fiction novel set during the Holocaust in Salonika, Greece. It follows Nico, a young boy renowned for his absolute honesty, who is tricked by a Nazi officer into lying to his Jewish community, urging them to board trains to their deaths. Haunted by this, Nico becomes a chronic liar, seeking atonement over decades', 'Fiction', 38.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777982863_img3.jpg', 0.00, 0, '2026-05-05 12:07:43', 0),
(6, 'The 7 Habits Of Highly Effective People', ' Stephen R. Covey', 'One of the most inspiring and impactful books ever written, The 7 Habits of Highly Effective People has captivated readers for nearly three decades. It has transformed the lives of presidents and CEOs, educators and parents—millions of people of all ages and occupations', 'Non-fiction', 49.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777989585_img1.jpg', 0.00, 0, '2026-05-05 13:59:45', 0),
(7, 'Talking To Strangers: What We Should Know About The People We Don\'t Know', ' Malcolm Gladwell', 'This book explores why humans are inept at understanding people they don\'t know, arguing that our misinterpretations of strangers\' intentions and behaviors often lead to societal conflict and tragedy.', 'Non-fiction', 42.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777989875_img2.jpg', 0.00, 0, '2026-05-05 14:04:35', 0),
(8, 'The Power Of A Positive Attitude', 'Roger Fritz', 'This book shows a practical guide designed to help readers achieve personal and professional success by developing a resilient, optimistic mindset.', 'Non-fiction', 28.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777990124_img3.jpg', 0.00, 0, '2026-05-05 14:08:44', 0),
(9, 'Stress-Free Productivity: A Personalised Toolkit to Become Your Most Efficient, Creative Self', 'Alice Boyes', 'This book shows a research-backed guide designed to help individuals build customized productivity systems that prioritize mental health. It moves away from one-size-fits-all advice, offering tools to manage perfectionism, cultivate self-compassion, and leverage unique personal quirks for efficient, creative work', 'Non-fiction', 25.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777990397_img4.jpg', 0.00, 0, '2026-05-05 14:13:17', 0),
(10, 'Just One Thing: How simple changes can transform your life', 'Dr Michael Mosley', 'The book shows outlines small, scientifically backed lifestyle adjustments that, when implemented daily, can significantly improve mental and physical health. It focuses on easy, sustainable habits (like cold showers, singing, or walking) over grand resolutions.', 'Non-fiction', 31.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777991316_img5.jpg', 0.00, 0, '2026-05-05 14:28:36', 0),
(11, '100 Facts Oceans', 'Clare Oliver', 'Take a deep breath and dive into an amazing watery world! Exactly 100 facts will help you discover everything you need to know about oceans. Learn about life beneath the waves, find out about underwater mountains and take a look at some crazy creatures of the deep.', 'Children', 15.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777992163_img1.jpg', 0.00, 0, '2026-05-05 14:42:02', 0),
(12, 'A Child\'s Introduction to Space Exploration: An Explorer\'s Guide to Rockets, Astronauts, and Life in Zero Gravity', 'Michael E Bakich', 'This book includes interactive 90-page guide for ages 8–12, covering the history, science, and future of space travel. It features facts on rockets, astronaut life, STEM experiments, and profiles of key figures, illustrated with NASA photos.', 'Children', 35.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777992400_img2.jpg', 0.00, 0, '2026-05-05 14:46:40', 0),
(13, 'A Friend To Nature', 'Laura Knowles', 'This book is a beautifully illustrated, hands-on guide for children aged 6–8. It encourages young readers to become eco-warriors through a \"friendship pledge\" and practical activities, such as building bird feeders and identifying local nature.', 'Children', 17.99, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777992759_img3.jpg', 0.00, 0, '2026-05-05 14:52:39', 0),
(14, 'Animal Explorers: Ivy The Bug Hunter', ' Sharon Rentta', 'A story about Ivy, an elephant who overcomes its dislike of bugs to become a passionate bug hunter. ', 'Children', 19.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777993047_img4.jpg', 0.00, 0, '2026-05-05 14:57:27', 0),
(15, 'Bob\'s Blue Period', 'Marion Deuchars', 'A story about Bob the bird, an artist who becomes deeply sad and paints only in blue after his best friend, Bat, goes away. Through themes of friendship and emotional expression, Bob\'s friends help him navigate his loneliness and find joy in color again.', 'Children', 24.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1777993233_img5.jpg', 0.00, 0, '2026-05-05 15:00:33', 0),
(16, 'Oxford Advanced Learner\'s Dictionary', 'Oxford', 'This book offers comprehensive definitions, example sentences, and tools for upper-intermediate to advanced learners (B2-C2), focusing on building vocabulary, improving pronunciation, and developing writing and speaking skills.', 'Education & Reference', 78.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778045690_img6.jpg', 0.00, 0, '2026-05-06 05:34:50', 0),
(17, 'How to Win Friends and Influence People', 'Dale Carnegie', 'It is a timeless guide emphasizing that success stems from managing emotions, practicing empathy, and making others feel important. The core message is to stop criticizing, offer sincere appreciation, and view situations from the other person\'s perspective to influence them positively.', 'Education & Reference', 40.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778046388_img1.jpg', 0.00, 0, '2026-05-06 05:46:28', 0),
(18, 'Thinking, Fast and Slow', 'Daniel Kahneman', 'This book explores two mental systems driving decisions: System 1 (fast, intuitive, emotional) and System 2 (slow, logical, effortful). Kahneman demonstrates that System 1 often leads to cognitive biases and errors, while System 2 is lazy and prone to allowing shortcuts, causing predictably irrational choices', 'Education & Reference', 46.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778046581_img2.jpg', 0.00, 0, '2026-05-06 05:49:41', 0),
(19, 'Cambridge IELTS Academic 18', 'Cambridge', 'This book provides four authentic examination papers from Cambridge University Press & Assessment, offering the most realistic practice for the Academic module. ', 'Education & Reference', 70.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778047023_img3.jpg', 0.00, 0, '2026-05-06 05:57:03', 0),
(20, 'How to Take Smart Notes', 'Sönke Ahrens', 'This book outlines the \"Zettelkasten\" (slip-box) method, a system for effective thinking, learning, and writing by connecting ideas rather than collecting information. It emphasizes creating permanent, atomized notes in your own words, linking them to existing notes, and organizing them bottom-up to develop ideas over time.', 'Education & Reference', 49.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778047708_img4.jpg', 0.00, 0, '2026-05-06 06:08:28', 0),
(21, 'The Science of Why We Exist: A History of the Universe from the Big Bang to Consciousness', 'Tim Coulson ', 'It is an accessible scientific narrative tracing the 13.8-billion-year journey from the Big Bang to human consciousness. It explores the improbable chain of physical, chemical, and biological events necessary for existence, examining whether human life was inevitable or a result of extraordinary luck.', 'Science & Technology', 85.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778048585_img1.jpg', 0.00, 0, '2026-05-06 06:23:05', 0),
(22, ' AI Valley: Microsoft, Google, and the Trillion-Dollar Race to Cash In on Artificial Intelligence', 'Gary Rivlin', 'This book is a journalistic account of the AI arms race, focusing on the personalities, venture capitalists, and tech giants (Google, Microsoft, Meta) competing to define the generative AI era.', 'Science & Technology', 90.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778048794_img2.jpg', 0.00, 0, '2026-05-06 06:26:34', 0),
(23, 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'This book shows outlines principles for writing software that is easy to read, maintain, and extend. ', 'Science & Technology', 52.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778050285_img3.jpg', 0.00, 0, '2026-05-06 06:51:25', 0),
(24, 'The Innovators: How a Group of Hackers, Geniuses, and Geeks Created the Digital Revolution', 'Walter Isaacson ', 'This book shows a comprehensive history of the digital revolution, highlighting that the computer and internet were created through collaboration, not just solo genius. ', 'Science & Technology', 78.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778051777_img4.jpg', 0.00, 0, '2026-05-06 07:16:17', 0),
(25, 'Everything Is Predictable: How Bayesian Statistics Explain Our World ', 'Tom Chivers ', 'This book explores Bayes\' theorem which posits that humans intuitively update beliefs based on new data. Chivers argues this \"Bayesian brain\" approach explains how we make decisions, navigate uncertainty, and understand the world across fields like medicine, AI, law, and climate science', 'Science & Technology', 65.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778052370_img5.jpg', 0.00, 0, '2026-05-06 07:26:10', 0),
(26, 'The Essentials Of Finance And Accounting For Nonfinancial Managers', 'Edward Fields', 'This book is a practical guide designed to help non-finance managers interpret financial data, understand annual reports, and make informed, profit-driven decisions.', 'Business & Finance', 23.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778054033_img1.jpg', 0.00, 0, '2026-05-06 07:53:53', 0),
(27, 'Buy Low, Sell High : The Simplicity Of Business Finance', 'Philip Young', 'This book has a concise guide designed to demystify corporate finance for non-financial managers and entrepreneurs. It focuses on essential financial measures, teaching how businesses make money, how to read financial health, and how to improve performance.', 'Business & Finance', 23.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778054371_img2.jpg', 0.00, 0, '2026-05-06 07:59:31', 0),
(28, 'Real Life Money: An Honest Guide to Taking Control of Your Finances', 'Clare Seal', 'This book is a compassionate, part-memoir guide focused on repairing one\'s relationship with money, tackling debt, and managing financial anxiety without sacrificing joy. It addresses the psychological, social, and practical causes of financial hardship.', 'Business & Finance', 20.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778054565_img3.jpg', 0.00, 0, '2026-05-06 08:02:45', 0),
(29, 'Cloudmoney: Cash, Cards, Crypto, And The War For Our Wallets', 'Brett Scott', 'This book exposes the coordinated campaign by Big Finance and tech companies to eliminate physical cash in favor of digital \"cloudmoney.\" It argues that a cashless society removes privacy, creates financial exclusion, and transfers power to tech corporations.', 'Business & Finance', 26.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778054811_img4.jpg', 0.00, 0, '2026-05-06 08:06:51', 0),
(30, 'Starting A Business From Home: Your Guide To Planning Your Home Start-Up', 'Colin Barrow', 'This book is a practical guide designed to help aspiring entrepreneurs plan, launch, and grow a business from home. It covers essential topics including market research, writing a business plan, raising capital, managing finances, building a website, and planning for expansion, offering actionable advice for creating a profitable enterprise.', 'Business & Finance', 48.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778055027_img5.jpg', 0.00, 0, '2026-05-06 08:10:27', 0),
(31, 'Awakenings - A Guide To Living A Vegan Lifestyle', 'Lucy Watson', 'This book has a comprehensive, practical guide that demystifies veganism, showing it is a holistic lifestyle rather than just a diet. ', 'Lifestyle (Health, Cooking, Arts)', 25.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778056860_img6.png', 0.00, 0, '2026-05-06 08:41:00', 0),
(32, 'Super Self-Care: How to Find Lasting Freedom from Addiction, Toxic Relationships and Dysfunctional Lifestyles', 'Christopher Dines', 'This book is a compassionate, practical guide for breaking free from addiction, toxic relationships, and dysfunctional behaviors. It emphasizes prioritizing mental, emotional, and spiritual well-being through mindfulness, self-compassion, and practical exercises, offering a roadmap to lasting recovery, inner peace, and authentic living.', 'Lifestyle (Health, Cooking, Arts)', 17.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778057238_img7.jpg', 0.00, 0, '2026-05-06 08:43:19', 0),
(33, 'Clean Eating', 'Igloobooks', 'This book shows a nutritional approach focusing on consuming whole, minimally processed, and natural foods, such as fresh fruits, vegetables, lean proteins, and whole grains. Key principles include mindful eating, reading labels, and choosing nutrient-dense options.', 'Lifestyle (Health, Cooking, Arts)', 21.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778057855_img8.jpg', 0.00, 0, '2026-05-06 08:57:35', 0),
(34, 'Green Living: A Comprehensive Guide to a Happy and Sustainable Life', 'Green Matters', 'This book is a practical, accessible guide offering actionable strategies to adopt an eco-friendly lifestyle. It covers waste reduction, sustainable fashion, non-toxic cleaning, and mindful consumption.', 'Lifestyle (Health, Cooking, Arts)', 40.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778064356_1778058293_img9.jpg', 0.00, 0, '2026-05-06 09:04:53', 0),
(35, 'Keto Kitchen: Delicious recipes for energy and weight loss', 'Monya Kilian Palmer', 'It is a comprehensive cookbook designed to make the low-carb, high-fat ketogenic lifestyle accessible, flavorful, and sustainable. The book aims to support weight loss and improve mental clarity through easy-to-follow recipes tailored for busy schedules.', 'Lifestyle (Health, Cooking, Arts)', 43.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778064332_1778058504_img10.jpg', 0.00, 0, '2026-05-06 09:08:24', 0),
(36, 'A Calamity Of Souls', 'David Baldacci', 'It is a 1968-set historical legal thriller about a Black Vietnam veteran, Jerome Washington, wrongfully accused of murdering a wealthy white couple in segregated Freeman County, Virginia. White lawyer Jack Lee and Black Chicago attorney Desiree DuBose team up to fight a corrupt system and save Washington from the electric chair, facing immense racial prejudice.', 'Fiction', 45.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778136293_img1.jpg', 0.00, 0, '2026-05-07 06:44:53', 0),
(37, 'Eruption', 'James Patterson', 'It is a techno-thriller by Michael Crichton and James Patterson, following a massive Mauna Loa eruption in Hawaii that threatens to expose a deadly, hidden Cold War-era chemical weapon. Scientists and military leaders race to avert a global ecological disaster as lava approaches the secret site.', 'Fiction', 39.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778137170_img2.jpg', 0.00, 0, '2026-05-07 06:59:30', 0),
(38, 'The Grandest Game', 'Jennifer Lynn Barnes', 'It is a story about heiress Avery Grambs and the Hawthorne brothers host a $26 million, high-stakes competition on a private island. Seven contestants, including newcomer POV characters Lyra, Gigi, and Rohan, solve dangerous puzzles while harboring secrets.', 'Fiction', 38.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778138134_img3.jpg', 0.00, 0, '2026-05-07 07:15:34', 0),
(39, 'One By One', ' Ruth Ware', 'It is a locked-room thriller where employees of a tech startup (\"Snoop\") are stranded by an avalanche at a luxurious French Alps ski chalet. Amidst tensions over a buyout, staff start dying one by one, shifting the story into a desperate battle for survival.', 'Fiction', 27.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778138718_img4.jpg', 0.00, 0, '2026-05-07 07:25:18', 0),
(40, 'True Colours', 'Kristin Hannah', 'It is a dramatic novel focusing on the Grey sisters—Winona, Aurora, and Vivi Ann—on their family\'s Washington state horse ranch. Following their mother\'s death, jealousy, betrayal, and a shocking crime shatter their bond, testing loyalties, forgiveness, and family, particularly when the youngest sister\'s life implodes.', 'Fiction', 32.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778139659_img6.jpg', 0.00, 0, '2026-05-07 07:40:59', 0),
(41, 'Generation Anxiety: A Millennial And Gen Z Guide To Staying Afloat In An Uncertain World', ' Dr Lauren Cook', 'This book shows a practical, evidence-based guide designed to help younger generations manage high anxiety levels caused by climate change, political instability, and financial pressures. It offers actionable tools, exercises, and personal insights to foster \"empowered acceptance\" in a chaotic world.', 'Non-fiction', 26.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778140421_img7.png', 0.00, 0, '2026-05-07 07:53:41', 0),
(42, 'Conflict Resilience: Negotiating Disagreement Without Giving Up Or Giving In', 'Robert Bordone', 'This book provides a research-backed framework for managing conflict by building the capacity to sit with discomfort, fostering deeper relationships rather than fleeing from or forcing resolution. It combines negotiation expertise with behavioral neurology to offer practical tools for navigating disagreements with integrity.', 'Non-fiction', 55.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778140930_img8.png', 0.00, 0, '2026-05-07 08:02:11', 0),
(43, 'Make Your Bed: Little Things That Can Change Your Life...And Maybe The World', 'Admiral William H. Mcraven', 'It is a motivational book based on his viral 2014 commencement speech. It offers 10 actionable lessons from Navy SEAL training, emphasizing that small, disciplined tasks—starting with making your bed—create positive habits, resilience, and a \"ripple effect\" to overcome challenges and achieve big goals', 'Non-fiction', 45.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778141432_img9.png', 0.00, 0, '2026-05-07 08:10:32', 0),
(44, 'Atomic Habits', 'James Clear', 'This book provides a framework for improving every day by focusing on tiny, incremental changes (1% better) rather than massive, overnight transformations. The core philosophy is that habits are the \"compound interest of self-improvement,\" where small, consistent actions (systems) compound over time to create, remarkable, long-term results.', 'Non-fiction', 30.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778141998_img2.jpg', 0.00, 0, '2026-05-07 08:19:58', 0),
(45, 'The Power Of Your Subconscious Mind', 'Joseph Murphy', 'This is a classic self-help book that teaches how to harness the subconscious mind to achieve success, health, and happiness. It highlights that by changing one’s inner thought patterns—through visualization, affirmation, and belief—one can positively influence their outer physical reality, relationships, and financial status.', 'Non-fiction', 25.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778142277_img3.jpg', 0.00, 0, '2026-05-07 08:24:37', 0),
(46, '100 Facts World Wonders', 'Adam Hibbert', 'It is a 48-page illustrated educational book for children (aged 7+) that presents 100 numbered, bite-sized facts about famous natural and man-made landmarks. It covers sites like ancient temples and modern skyscrapers, featuring detailed photos, cartoons, quizzes, and projects.', 'Children', 19.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778142557_img4.jpg', 0.00, 0, '2026-05-07 08:29:17', 0),
(47, 'Aliens and Other Worlds: True Tales from Our Solar System and Beyond', 'Lisa Harvey-Smith', ' it is an engaging, illustrated children book exploring the search for extraterrestrial life. It examines where to find aliens, what they might look like, and if they live among us.', 'Children', 28.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778142850_img5.jpg', 0.00, 0, '2026-05-07 08:34:10', 0),
(48, 'All The Animals Were Sleeping', 'Clare Helen Welsh', 'It is a soothing bedtime picture book featuring a little mongoose traversing the Serengeti as night falls. As he heads home, he observes various animals sleeping in unique ways, offering a gentle, rhythmic, and informative tale about nature and rest.', 'Children', 14.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778143050_img6.jpg', 0.00, 0, '2026-05-07 08:37:30', 0),
(49, 'Be Yourself: Why It\'s Great to Be You', 'Poppy O\'Neill', 'It is a practical, engaging guide designed for children aged 7–11 to foster self-acceptance, build confidence, and embrace individuality. Featuring a supportive character named Glow, the book uses cognitive behavioral therapy (CBT) and mindfulness techniques to help children manage negative thoughts and peer pressure.', 'Children', 27.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778143252_img7.jpg', 0.00, 0, '2026-05-07 08:40:52', 0),
(50, 'Fluffles: The Brave Koala Who Held Strong Through a Bushfire', 'Vita Murrow', 'It is a heartwarming, true-based children\'s picture book about a koala surviving the 2020 Australian bushfires. It follows Fluffles as she escapes flames by climbing to the top of a tree, gets rescued with burnt paws, and heals by snuggling with other koalas.', 'Children', 26.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778143466_img8.jpg', 0.00, 0, '2026-05-07 08:44:26', 0),
(51, 'The Merriam-Webster Dictionary', 'Merriam-Webster', 'It is a premier, authoritative American English reference, featuring over 75,000 clear, concise definitions, 8,000+ usage examples, and extensive word histories. Updated regularly to include new vocabulary across science, technology, and culture, it serves as a reliable guide for spelling, pronunciation, and synonyms.', 'Education & Reference', 65.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778144442_img9.jpg', 0.00, 0, '2026-05-07 09:00:01', 0),
(52, 'How to win at College: Simple Rules for Success From Star Students', 'Cal Newport ', 'This book is a guide offering 75 actionable, unconventional strategies to excel academically and socially without burning out. Based on interviews with successful students, it emphasizes working smarter, building a distinct identity, and enjoying the college experience.', 'Education & Reference', 72.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778144736_img10.jpg', 0.00, 0, '2026-05-07 09:05:36', 0),
(53, 'The Elements of Style - Illustrated', 'William Strunk Jr. & E.B. White', 'This book is a classic writing guide, featuring 57 whimsical, colorful illustrations by Maira Kalman. It combines the original, authoritative rules on grammar, composition, and style with a vibrant visual interpretation, making the instructional content more engaging and accessible.', 'Education & Reference', 65.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778145748_img11.jpg', 0.00, 0, '2026-05-07 09:22:28', 0),
(54, 'The Study Skills Handbook', 'Stella Cottrell ', 'This book is a comprehensive, practical guide designed to help university students optimize their learning, build confidence, and boost employability. It offers tailored strategies for time management, critical thinking, academic writing, and note-making.', 'Education & Reference', 78.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778146548_img12.jpg', 0.00, 0, '2026-05-07 09:35:48', 0),
(55, 'A Manual for Writers of Research Papers', 'Kate L. Turabian', 'This book is the definitive, comprehensive guide for students and researchers on crafting, formatting, and citing academic papers. It provides a three-part framework covering the research process, citation styles (notes-bibliography and author-date), and editorial style, aligning with Chicago Manual of Style.', 'Education & Reference', 80.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778147150_img13.jpg', 0.00, 0, '2026-05-07 09:45:50', 0),
(56, 'The Doomsday Book: The Science Behind Humanity\'s Greatest Threats', 'Marshall Brain', 'This book is an illustrated 288-page exploration of potential existential risks to human civilization. It examines natural, manmade, and science-fiction scenarios—such as pandemics, nuclear war, AI, and asteroid impacts—providing scientific explanations, impact analysis, and potential mitigation strategies.', 'Science & Technology', 30.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778149061_img14.jpg', 0.00, 0, '2026-05-07 10:17:41', 0),
(57, 'The Big Ideas in Science: A complete introduction', 'Jon Evans', 'This book is an accessible guide, part of the Teach Yourself series, providing a comprehensive overview of fundamental scientific concepts. Covering topics from the Big Bang to modern technology, it explains key ideas in physics, biology, chemistry, and environmental science for a general audience.', 'Science & Technology', 21.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778149850_img15.jpg', 0.00, 0, '2026-05-07 10:30:50', 0),
(58, 'Wild Weather: The Myths, Science & Wonder of Weather', 'Alison Davies', 'This book is a beautifully illustrated, 144-page guide exploring meteorological phenomena, folklore, and myth. It explains how weather events like rain, wind, and lightning occur, while offering a fun, accessible approach to connecting with nature and embracing different weather conditions.', 'Science & Technology', 23.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778150675_img16.jpg', 0.00, 0, '2026-05-07 10:44:35', 0),
(59, 'Youniverse: A Short Guide to Modern Science', 'Elsie Burch Donald', 'This book is an accessible, 240-page primer explaining fundamental scientific concepts from the Big Bang to AI. It explores humanity\'s place in the universe using plain language, short chapters, and minimal jargon, focusing on topics like matter, energy, and evolution, all vetted by experts.', 'Science & Technology', 24.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778150913_img17.jpg', 0.00, 0, '2026-05-07 10:48:33', 0),
(60, 'Seeing Science: The Art Of Making The Invisible Visible', 'Jack Challoner', 'This book is a visually driven exploration of how scientists use imaging technologies to make hidden, abstract, or microscopic phenomena tangible. With over 200 color images, it explores how we visualize everything from atomic structures to cosmic events.', 'Science & Technology', 22.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778151255_img19.jpg', 0.00, 0, '2026-05-07 10:53:47', 0),
(61, 'Starting A Successful Business: Your Guide To Setting Up Your Dream Start-Up, Controlling Its Finances And Managing Its Operations (Business Success)', 'Michael J. Morris', 'This book offers a practical guide to turning business ideas into profitable, long-term ventures. It provides essential advice on planning, marketing, and controlling finances, helping entrepreneurs avoid pitfalls in the crucial first 18 months.', 'Business & Finance', 40.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778159621_img1.jpg', 0.00, 0, '2026-05-07 13:13:41', 0),
(62, 'From Monk To Money Manager: A Former Monk\'s Financial Guide To Becoming A Little Bit Wealthy - And Why That\'s Okay', 'Doug Lynam', 'This book is a financial guide blending spiritual wisdom with practical investment advice, arguing that building moderate wealth is a moral, empowering act. Lynam, a former Benedictine monk turned financial advisor, advocates for mindful, long-term, low-cost investing to achieve financial freedom and better help others.', 'Business & Finance', 18.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778160592_img2.jpg', 0.00, 0, '2026-05-07 13:23:18', 0),
(63, 'Financially Forward: How To Use Today\'S Digital Tools To Earn More, Save Better, And Spend Smarter', 'Alexa Von Tobel', 'This book provides a straightforward guide to optimizing personal finances using modern technology. It provides actionable advice on leveraging smartphone apps, automation, and digital banking to manage money more efficiently, aiming to increase savings and improve spending habits in a digital economy.', 'Business & Finance', 26.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778160573_img3.jpg', 0.00, 0, '2026-05-07 13:29:22', 0),
(64, 'How to Write a Business Plan: Win Backing and Support for Your Ideas and Ventures', 'Brian Finch', 'This book provides a practical guide for entrepreneurs to create compelling plans that win investor backing. The 7th edition emphasizes using a 15–25 page format (or 1-page summary) packed with realistic data, market analysis, and clear financial projections to prove viability, specifically addressing the key questions potential backers ask.', 'Business & Finance', 22.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778160873_img4.jpg', 0.00, 0, '2026-05-07 13:34:33', 0),
(65, 'Anthro-Vision: A New Way To See In Business And Life', 'Gillian Tett', 'This book argues that applying anthropological methods—empathy, observation, and \"making the familiar strange\"—helps leaders navigate a complex world better than relying solely on big data or economic models. Tett demonstrates how understanding human culture uncovers hidden behaviors and drives better innovation.', 'Business & Finance', 29.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778161291_img5.jpg', 0.00, 0, '2026-05-07 13:41:31', 0),
(66, 'The Lazarus Strategy: How To Age Well And Wisely', ' Norman Lazarus', 'This book is a \"part how-to, part manifesto\" that challenges the idea that aging must mean inevitable physical and mental decline. Dr. Lazarus, an 84-year-old expert in exercise physiology, uses his own active, medication-free life as proof that later years can be vibrant and productive.', 'Lifestyle (Health, Cooking, Arts)', 20.50, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778162554_img6.jpg', 0.00, 0, '2026-05-07 14:02:34', 0),
(67, 'Real Food By Mike', 'Mike McEnearney', 'It is a seasonal cookbook focusing on wholefoods, wellbeing, and the concept of a \"physic garden\". It offers fresh, delicious recipes designed to improve long-term health while celebrating nutritious, enjoyable eating through dishes that are both indulgent and healthy.', 'Lifestyle (Health, Cooking, Arts)', 32.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778162999_img7.jpg', 0.00, 0, '2026-05-07 14:09:59', 0),
(68, 'Art Of Blending', 'Tori Ritchie', 'It  is a versatile cookbook designed to help owners of high-performance blenders (specifically the Vitamix Professional Series) move beyond basic smoothies. The book positions the pro-blender as a multi-functional tool capable of acting as a food processor, ice cream maker, and even a stove for heating soups.', 'Lifestyle (Health, Cooking, Arts)', 26.90, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778163410_img8.jpg', 0.00, 0, '2026-05-07 14:16:50', 0),
(69, 'The Art Of Healthy Food - Gluten Free', 'Jasmin Peppiatt', 'It is a 256-page cookbook designed to help readers boost energy and lose weight by adopting a gluten-free lifestyle. It provides practical information for reducing or eliminating gluten, featuring various recipes aimed at making healthy, gluten-free eating accessible and enjoyable.', 'Lifestyle (Health, Cooking, Arts)', 23.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778163687_img9.jpg', 0.00, 0, '2026-05-07 14:21:27', 0),
(70, '[Bargain Corner] The Art Of Healthy Food - Dairy Free', 'Jasmin Peppiatt', 'It is a paperback cookbook featuring dairy-free recipes, designed for those with allergies, intolerances, or seeking healthier lifestyle alternatives. It is part of a series focusing on easy, nutritious meals with inspirational imagery, often sold at discounted prices.', 'Lifestyle (Health, Cooking, Arts)', 16.00, 20, '/finalproject/booknestonlinebookstoresystem/Image/1778163928_img10.jpg', 0.00, 0, '2026-05-07 14:25:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_proof` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','staff','admin') NOT NULL DEFAULT 'customer',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `email_verified`, `verification_token`, `reset_token`, `reset_expires`, `created_at`) VALUES
(2, 'PHANG YU XUE', 'phangyuxue@gmail.com', '$2y$10$bh7bl58wdgwd23ggt8ejKuhXh8T7HWMwvAOHkaQO1bsqGp35jXBk.', 'customer', 1, NULL, NULL, NULL, '2026-05-03 15:01:56'),
(3, 'Admin', 'admin123@gmail.com', '$2y$10$QbcEToCewtvC62TVcDsFoON/oRceRGEKS.KvIOWwJ4/xfEURG6shy', 'admin', 1, NULL, NULL, NULL, '2026-05-05 08:13:20'),
(4, 'Staff_1', 'staff123@gmail.com', '$2y$10$kt/IDtTosIsUrF.h99tW8uVKUe.TDq4xvDVnRoh9YNd8dprhWTI.2', 'staff', 1, NULL, '10d38dadc75bcc7332b8586fe57cabb2c07b60cae86e2002156a3cb6363f52fe', '2026-05-07 12:41:40', '2026-05-05 13:44:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
