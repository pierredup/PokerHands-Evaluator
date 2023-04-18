<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Console\Command;

use Rsaweb\Poker\Contracts\Suite;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_combine;
use function array_count_values;
use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_intersect;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function implode;
use function sprintf;

#[AsCommand(
    name: 'poker:evaluate',
    description: 'Evaluate a poker hand',
)]
final class EvaluateCommand extends Command
{
    private const MAX_CARDS = 5;

    private readonly SymfonyStyle $io;

    /**
     * A map of all the suites converted to string with the short notation as the key
     * E.G. ['2H' => 'Two of Hearts']
     *
     * @var array<string, Suite>
     */
    private array $allSuites;

    /**
     * List of all the suite shorthand keys for easier reference
     *
     * @var string[]
     */
    private array $suiteKeys;

    protected function configure(): void
    {
        $maxCards = self::MAX_CARDS;

        $this
            ->setHelp(<<<HELP
The <info>%command.name%</info> command evaluates a poker hand.

Pass in the suites as a space separated list, with a list of $maxCards cards.

Use the shorthand notation for a card, E.G. 2H for 2 of Hearts.

<info>php %command.full_name% 2H KC 4D 10S AH</info>
HELP)
            ->addArgument(
                name: 'suites',
                mode: InputArgument::IS_ARRAY,
                description: 'The suites to evaluate. Use shorthand notation for each suite (E.G 2H for 2 of Hearts)',
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $suites = array_merge(
            Spade::cases(),
            Diamond::cases(),
            Heart::cases(),
            Club::cases(),
        );

        $this->suiteKeys = array_map(static fn(Suite $suite) => $suite->toShortString(), $suites);
        $this->allSuites = array_combine($this->suiteKeys, $suites);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->validateInput($input->getArgument('suites'))) {
            return self::FAILURE;
        }

        $this->io->success('You chose the following cards:');

        $this->io->listing(
            array_map(
                fn(string $suite) => $this->allSuites[$suite]->toString(),
                $input->getArgument('suites')
            )
        );

        return self::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $options = $input->getArgument('suites');

        if (count($options) > self::MAX_CARDS) {
            return;
        }

        // Create initial set of choices, removing any choices already passed in as arguments
        $choices = array_diff_key(
            array_map(static fn(Suite $suite) => $suite->toString(), $this->allSuites),
            array_flip($options)
        );

        while (count($options) !== self::MAX_CARDS) {
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

            if (count($options) > self::MAX_CARDS) {
                $this->io->warning(sprintf('You chose more than %1$d cards. Only the first %1$d will be used.', self::MAX_CARDS));

                $options = array_slice($options, 0, self::MAX_CARDS);
                break;
            }

            // Remove chosen values so that they cannot be selected again
            $choices = array_diff_key($choices, array_flip($options));
        }

        $input->setArgument('suites', $options);
    }

    private function validateInput(array $suites): bool
    {
        if (count(array_intersect($suites, $this->suiteKeys)) !== self::MAX_CARDS) {
            // Get list of all invalid cards
            $invalidCards = array_diff($suites, $this->suiteKeys);

            $this->io->error(sprintf('You must choose %d valid cards. The following cards are invalid: %s', self::MAX_CARDS, implode(', ', $invalidCards)));
            return false;
        }

        if (count(array_unique($suites)) !== self::MAX_CARDS) {
            $duplicateCards = array_keys(
                array_filter(
                    array_count_values($suites),
                    static fn(int $value) => $value > 1
                )
            );

            $this->io->error(sprintf('You must choose %d unique cards. The following cards are duplicated: %s', self::MAX_CARDS, implode(', ', $duplicateCards)));
            return false;
        }

        return true;
    }
}
