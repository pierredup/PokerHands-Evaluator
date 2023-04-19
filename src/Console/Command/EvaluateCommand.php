<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Console\Command;

use Rsaweb\Poker\Contracts\Card;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Evaluate\PokerHandsEvaluate;
use Rsaweb\Poker\Exception\PokerHandsException;
use Rsaweb\Poker\Transformer\ShortStringToCardTransformer;
use Rsaweb\Poker\Transformer\TransformerInterface;
use Rsaweb\Poker\Validator\PokerHandValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_combine;
use function array_diff_key;
use function array_flip;
use function array_map;
use function array_merge;
use function count;
use function sprintf;

#[AsCommand(
    name: 'poker:evaluate',
    description: 'Evaluate a poker hand',
)]
final class EvaluateCommand extends Command
{
    private readonly SymfonyStyle $io;

    private array $allCards;
    private TransformerInterface $transformer;

    protected function configure(): void
    {
        $maxCards = PokerHandValidator::MAX_CARDS;

        $this
            ->setHelp(<<<HELP
The <info>%command.name%</info> command evaluates a poker hand.

Pass in the cards as a space separated list, with a list of $maxCards cards.

Use the shorthand notation for a card, E.G. 2H for 2 of Hearts.

<info>php %command.full_name% 2H KC 4D 10S AH</info>
HELP)
            ->addArgument(
                name: 'cards',
                mode: InputArgument::IS_ARRAY,
                description: 'The cards to evaluate. Use shorthand notation for each card (E.G 2H for 2 of Hearts)',
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->transformer = new ShortStringToCardTransformer();

        $cards = array_merge(
            Spade::cases(),
            Diamond::cases(),
            Heart::cases(),
            Club::cases(),
        );

        $this->allCards = array_combine(
            array_map(static fn(Card $card) => $card->toShortString(), $cards),
            $cards
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            PokerHandValidator::validate(...$input->getArgument('cards'));
        } catch (PokerHandsException $e) {
            $this->io->error($e->getMessage());

            return self::FAILURE;
        }

        $selectedCards = $this->transformer->transformArray($input->getArgument('cards'));

        $this->io->title('You chose the following cards:');

        $this->io->listing(
            array_map(
                static fn (Card $card) => $card->toString(),
                $selectedCards
            )
        );

        $this->io->title('The highest hand you have is:');
        $this->io->block(
            (new PokerHandsEvaluate(...$selectedCards))
                ->getHighestRank()
                ->toString()
        );

        return self::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $options = $input->getArgument('cards');

        if (count($options) > PokerHandValidator::MAX_CARDS) {
            return;
        }

        // Create initial set of choices, removing any choices already passed in as arguments
        $choices = array_diff_key(
            array_map(static fn(Card $card) => $card->toString(), $this->allCards),
            array_flip($options)
        );

        while (count($options) !== PokerHandValidator::MAX_CARDS) {
            $options = [
                ...$options,
                ...$this->io->askQuestion(
                    (new ChoiceQuestion(
                        question: $options ? 'Choose another card' : 'Choose a card',
                        choices: $choices
                    ))
                        ->setMultiselect(true)
                ),
            ];

            if (count($options) > PokerHandValidator::MAX_CARDS) {
                $this->io->warning(sprintf('You chose more than %1$d cards. Only the first %1$d will be used.', PokerHandValidator::MAX_CARDS));

                $options = array_slice($options, 0, PokerHandValidator::MAX_CARDS);
                break;
            }

            // Remove chosen values so that they cannot be selected again
            $choices = array_diff_key($choices, array_flip($options));
        }

        $input->setArgument('cards', $options);
    }
}
