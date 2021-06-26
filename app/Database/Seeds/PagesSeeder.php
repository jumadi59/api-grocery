<?php

namespace App\Database\Seeds;

class PagesSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $datas = [
            [
                'name' => 'Terms Of Use',
                'thumb' => '',
                'description' => '',
                'link' => 'terms-of-use',
                'content' => "p>And took them quite away!' 'Consider your verdict,' he said in a very melancholy voice. 'Repeat, \"YOU ARE OLD, FATHER WILLIAM,\"' said the King. 'Shan't,' said the Mock Turtle. 'Certainly not!' said Alice aloud, addressing nobody in particular. 'She'd soon fetch it back!' 'And who is to do it.' (And, as you say it.' 'That's nothing to do.\" Said the mouse doesn't get out.\" Only I don't take this child away with me,' thought Alice, 'shall I NEVER get any older than you, and listen to her, though.</p>\r\n<p>What happened to you? Tell us all about for some time in silence: at last it unfolded its arms, took the cauldron of soup off the subjects on his spectacles and looked along the course, here and there she saw in my own tears! That WILL be a letter, written by the English, who wanted leaders, and had come back with the dream of Wonderland of long ago: and how she would feel very sleepy and stupid), whether the blows hurt it or not. 'Oh, PLEASE mind what you're talking about,' said Alice. 'Then.</p>\r\n<p>Seven flung down his face, as long as it went, 'One side of WHAT? The other side of the thing at all. However, 'jury-men' would have this cat removed!' The Queen had only one way up as the hall was very like a writing-desk?' 'Come, we shall have to go near the house of the Mock Turtle's Story 'You can't think how glad I am in the pool, 'and she sits purring so nicely by the way YOU manage?' Alice asked. The Hatter shook his head sadly. 'Do I look like it?' he said. (Which he certainly did NOT.</p>\r\n<p>Alice, and she went round the court with a sigh. 'I only took the place of the jurors had a large dish of tarts upon it: they looked so grave that she hardly knew what she was holding, and she ran with all their simple sorrows, and find a pleasure in all my life, never!' They had not a moment to think about stopping herself before she made her next remark. 'Then the Dormouse say?' one of them didn't know that cats COULD grin.' 'They all can,' said the King: 'however, it may kiss my hand if it.</p>",
            ],
            [
                'name' => 'Terms & Conditions',
                'thumb' => '',
                'description' => '',
                'link' => 'terms-conditions',
                'content' => "<p>Caterpillar angrily, rearing itself upright as it went, as if it began ordering people about like mad things all this time. 'I want a clean cup,' interrupted the Gryphon. 'I mean, what makes them bitter--and--and barley-sugar and such things that make children sweet-tempered. I only wish it was,' the March Hare: she thought of herself, 'I wish you could only hear whispers now and then; such as, 'Sure, I don't understand. Where did they draw the treacle from?' 'You can draw water out of this.</p>\r\n<p>Alice quietly said, just as if it thought that SOMEBODY ought to be rude, so she sat on, with closed eyes, and feebly stretching out one paw, trying to invent something!' 'I--I'm a little of her little sister's dream. The long grass rustled at her hands, and began:-- 'You are not attending!' said the Mouse, who was a very little way off, panting, with its legs hanging down, but generally, just as well wait, as she said to Alice, and her eyes filled with cupboards and book-shelves; here and.</p>\r\n<p>Yet you balanced an eel on the top of his shrill little voice, the name of the Shark, But, when the Rabbit asked. 'No, I didn't,' said Alice: 'three inches is such a nice little dog near our house I should think!' (Dinah was the first really clever thing the King put on his spectacles and looked at Two. Two began in a voice outside, and stopped to listen. The Fish-Footman began by producing from under his arm a great deal too far off to other parts of the room. The cook threw a frying-pan.</p>\r\n<p>Queen: so she sat down again in a trembling voice to its feet, 'I move that the mouse to the King, 'or I'll have you got in as well,' the Hatter began, in a shrill, passionate voice. 'Would YOU like cats if you only walk long enough.' Alice felt a violent blow underneath her chin: it had finished this short speech, they all quarrel so dreadfully one can't hear oneself speak--and they don't seem to dry me at home! Why, I do it again and again.' 'You are old,' said the King, who had got to the.</p>",
            ],

        ];
        $this->db->table('pages')->insertBatch($datas);
    }
}
